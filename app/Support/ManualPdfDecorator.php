<?php

namespace App\Support;

use Dompdf\Dompdf;

/**
 * Minimal running footer for the system User Manual PDF — just a page
 * number and the document title, repeating on every page. Deliberately
 * NOT SchoolPdfDecorator: that one draws the Bible-verse/Vision-Mission
 * footer block used by formal financial/report PDFs, which doesn't suit
 * an 80-page software manual repeated on every single page.
 *
 * Uses Canvas::page_text()/page_line() directly (not page_script()) since
 * there's no per-page conditional logic needed here — both of those
 * methods already repeat on every page on their own and support the
 * {PAGE_NUM}/{PAGE_COUNT} placeholder tokens natively.
 */
class ManualPdfDecorator
{
    private const PAGE_WIDTH = 612.0;
    private const MARGIN_X = 54.0;
    private const COLOR_NAVY = [0.102, 0.227, 0.424];
    private const COLOR_GREY = [0.55, 0.55, 0.55];

    public static function apply(Dompdf $dompdf, string $documentTitle): void
    {
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $normal = $fontMetrics->getFont('DejaVu Sans', 'normal');

        $pageHeight = $canvas->get_height();
        $footerY = $pageHeight - 36.0;
        $left = self::MARGIN_X;
        $right = self::PAGE_WIDTH - self::MARGIN_X;

        $canvas->page_line($left, $footerY, $right, $footerY, [0.85, 0.85, 0.85], 0.5);
        $canvas->page_text($left, $footerY + 6, $documentTitle, $normal, 8, self::COLOR_GREY);

        $pageLabel = 'Page {PAGE_NUM} of {PAGE_COUNT}';
        $labelWidth = $fontMetrics->getTextWidth('Page 00 of 00', $normal, 8);
        $canvas->page_text($right - $labelWidth, $footerY + 6, $pageLabel, $normal, 8, self::COLOR_GREY);
    }
}
