{{-- Shared <style> block for every school-report PDF (Finance, Cashier,
     and any future portal that renders reports this way). DomPDF only supports
     a CSS subset (no flexbox/grid), so layout here uses display:table the
     same way resources/views/pdf/sf10.blade.php already does.

     Font: 'DejaVu Sans' (bundled with DomPDF), not Arial/Helvetica — those
     map to DomPDF's built-in Type1 fonts, which have no ₱ (Philippine Peso,
     U+20B1) glyph and silently render it as "?". DejaVu Sans has full
     Unicode coverage, same fix used system-wide for this.

     Margin: via @page{margin:...} in pt, not body{padding:...}. This has to
     be the single source of truth for the page content box now, because
     App\Support\SchoolPdfDecorator draws the running header/footer on the
     canvas layer using raw pt coordinates — those have to line up with
     whatever reserves the blank space for them, in the same unit, or they
     drift apart. The marginTop/Right/Bottom/Left keys Dompdf::setOptions()
     accepts aren't real Dompdf options at all (checked
     vendor/dompdf/dompdf/src/Options.php — no such properties exist), so
     that was always a silent no-op anyway.

     The explicit html{margin:...} rule right below, duplicating the same
     values, is NOT redundant — do not remove it. Traced through
     vendor/dompdf/dompdf/src/Css/Stylesheet.php (~line 1087-1236): @page's
     margin is implemented by assigning it as the ROOT <html> frame's own
     `margin` CSS property, not as a separate page-geometry concept. The
     universal `* { margin: 0; ... }` reset above matches <html> too and, by
     normal cascade rules, silently stomped that value back to 0 — confirmed
     directly by dumping the parsed root frame's computed style, and by a
     pdftoppm-rendered isolated test that showed a real 70-row table
     ignoring every @page margin value tried until this html{} override was
     added. A plain type selector (`html`) beats a universal selector (`*`)
     on specificity regardless of source order, so this makes the margin
     stick without having to touch the reset rule itself. Confirmed via the
     same isolated test that the margin then correctly repeats on every
     page, not just the first. --}}
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    @page { margin: 56pt 46pt 130pt 46pt; }
    html { margin: 56pt 46pt 130pt 46pt; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; }

    {{-- .fin-header-inner is an inline-table (shrinks to fit the logo+text
         content, not stretched full-width) so text-align:center on the
         outer .fin-header centers the whole logo+text group as ONE unit on
         the page — rather than the logo sitting at the left margin with the
         text stretching across the remaining width, which is what a
         width:100% table here would do. --}}
    .fin-header { text-align: center; border-bottom: 3px solid #1a3a6c; padding-bottom: 12px; margin-bottom: 10px; }
    .fin-header-inner { display: inline-table; border-spacing: 0; }
    {{-- Logo cell width matches the image exactly (56px = 56px) — an image
         wider than its table-cell forces dompdf to grow the cell to fit it,
         which was quietly leaving slack on the right no matter how far
         right the image itself was pushed. Checked the source PNG's actual
         opaque bounding box (public/images/logo.png) — it already fills
         nearly the whole canvas (2px padding each side), so the gap wasn't
         the artwork's fault, it was this cell/image size mismatch. --}}
    .fin-header-logo { display: table-cell; width: 56px; vertical-align: middle; }
    .fin-header-logo img { width: 56px; height: 56px; object-fit: contain; display: block; }
    .fin-header-text { display: table-cell; vertical-align: middle; text-align: center; padding-left: 6px; }
    .fin-school-name { font-size: 17px; font-weight: bold; color: #1a3a6c; }
    .fin-school-addr { font-size: 10.5px; color: #555; margin-top: 1px; }

    .fin-title-bar { text-align: center; margin: 10px 0 4px; }
    .fin-report-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; color: #2471a3; }
    .fin-title-divider { width: 46px; height: 3px; background: #1a3a6c; margin: 7px auto 0; border-radius: 2px; }
    .fin-report-range { font-size: 11px; color: #666; margin-top: 10px; text-align: center; }

    .fin-meta-row { display: table; width: 100%; font-size: 9.5px; color: #666; margin: 10px 0 16px; border-bottom: 1px solid #ddd; padding-bottom: 6px; }
    .fin-meta-row span:first-child { display: table-cell; text-align: left; }
    .fin-meta-row span:last-child { display: table-cell; text-align: right; }

    .fin-stats { display: table; width: 100%; margin-bottom: 16px; border-spacing: 6px 0; }
    .fin-stat-cell { display: table-cell; width: 25%; background: #f0f4ff; border: 1px solid #c7d7ff; border-radius: 4px; padding: 8px 10px; text-align: center; }
    .fin-stat-value { font-size: 15px; font-weight: bold; color: #1a3a6c; }
    .fin-stat-label { font-size: 8.5px; color: #666; text-transform: uppercase; margin-top: 2px; }

    table.fin-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.fin-table th { background: #1a3a6c; color: #fff; font-size: 9px; text-transform: uppercase; padding: 6px 8px; text-align: left; border: 1px solid #16315c; }
    table.fin-table td { border: 1px solid #ddd; padding: 5px 8px; font-size: 9.5px; vertical-align: top; }
    table.fin-table tr:nth-child(even) td { background: #f7f9fc; }
    table.fin-table tfoot td { background: #eef2fb; font-weight: bold; border-top: 2px solid #1a3a6c; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    {{-- .fin-bottom holds just the signature lines now — the Bible verse +
         Vision/Mission + print-meta footer moved to
         App\Support\SchoolPdfDecorator, which draws it on the canvas layer
         on every page (CSS position:fixed was tried for this and reverted;
         see the @page comment above). This block stays in normal HTML flow
         so it lands on the true last page, right after the report's last
         content, below where the canvas footer band is reserved.
         page-break-inside:avoid keeps the two signature cells from being
         split across a page boundary. --}}
    .fin-bottom { margin-top: 24px; page-break-inside: avoid; }

    .fin-sig-block { display: table; width: 100%; border-spacing: 20px 0; }
    .fin-sig-cell { display: table-cell; width: 50%; text-align: center; }
    .fin-sig-line { border-top: 1px solid #333; padding-top: 4px; font-size: 10px; min-height: 14px; }
    .fin-sig-label { font-size: 8.5px; color: #666; text-transform: uppercase; margin-top: 2px; }
</style>
