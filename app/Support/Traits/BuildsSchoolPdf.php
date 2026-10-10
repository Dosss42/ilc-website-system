<?php

namespace App\Support\Traits;

/**
 * Shared letterhead data + Dompdf wiring for every portal that generates a
 * school-report PDF on the pdf.shared.* partials (Finance, Cashier, and
 * whatever's next). A controller using this trait adds one small wrapper
 * for its own "who generated this" lookup (e.g. which auth guard), then
 * calls schoolPdfOptions() exactly like Finance\DashboardController does —
 * see that class's financePdfLetterhead()/financePdfOptions() for the
 * reference usage this was extracted from.
 */
trait BuildsSchoolPdf
{
    private function schoolPdfLetterhead(?string $generatedByName = null): array
    {
        $settings = \App\Models\Setting::pluck('value', 'key');
        $logoPath = public_path('images/logo.png');

        return [
            'schoolLogoBase64' => file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null,
            'schoolName'    => $settings['school_name'] ?? 'IEMELIF Learning Center',
            'schoolAddress' => $settings['school_address'] ?? 'General Tinio, Nueva Ecija',
            'generatedBy'   => $generatedByName ?? 'Staff',
            'generatedAt'   => now(),
        ];
    }

    /**
     * Shared Dompdf options for every school-report PDF — letter size, no
     * remote assets (logo is already inlined as base64 by the caller) —
     * plus the running header/footer, drawn on the canvas layer by
     * App\Support\SchoolPdfDecorator from the same $meta array the view
     * was given (needs schoolName, reportTitle, generatedAt).
     */
    private function schoolPdfOptions(\Barryvdh\DomPDF\PDF $pdf, array $meta = []): \Barryvdh\DomPDF\PDF
    {
        // Dompdf's table/font-reflow pass is memory-hungry, and this app's
        // local Apache+PHP (WAMP) is configured with the stock 128M
        // memory_limit — confirmed by reproducing a real "Allowed memory
        // size of 134217728 bytes exhausted" fatal generating an 8-page,
        // 214-row report. A real browser request to a bigger report would
        // hit the exact same wall (unlike CLI/tinker testing, which can be
        // run with a raised -d memory_limit flag — production requests
        // can't). @-suppressed: ini_set() is disabled outright on some
        // hosts, and this must stay non-fatal either way.
        @ini_set('memory_limit', '512M');

        $pdf->setPaper('letter', 'portrait');
        // Page margins are set via @page{margin:...} in pdf.shared._styles,
        // not here — Dompdf\Options has no marginTop/Right/Bottom/Left
        // properties (checked vendor/dompdf/dompdf/src/Options.php), so
        // passing them through setOptions() used to be a silent no-op.
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultPaperSize'     => 'letter',
        ]);
        if ($meta !== []) {
            // Canvas::page_script() loops over whatever pages already exist
            // at the moment it's called (vendor/dompdf/dompdf/src/Adapter/
            // CPDF.php processPageScript()) — it is NOT a lazy "run on every
            // future page" hook. Calling it before layout/pagination has
            // happened only ever sees page 1, so the decorator would only
            // draw on the first page of a multi-page report.
            // ->render() here forces pagination to finish first; the PDF
            // wrapper's own render()/output() guards on an internal
            // $rendered flag, so a later ->download()/->stream() call on
            // this return value won't render a second time.
            $pdf->render();
            \App\Support\SchoolPdfDecorator::apply($pdf->getDomPDF(), $meta);
        }
        return $pdf;
    }
}
