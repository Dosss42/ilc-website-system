<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Canvas;
use Dompdf\FontMetrics;

/**
 * Draws a running header (pages 2+) and footer (every page) on every
 * school-report PDF — Finance, Cashier, and any future portal that
 * renders reports this way — via Dompdf's page_script canvas hook rather
 * than CSS.
 *
 * CSS position:fixed was tried first and reverted — Dompdf's table
 * auto-pagination doesn't reserve space for fixed-position content, so a
 * long report's last rows ran straight through the fixed footer (confirmed
 * with two separate isolated, pdftoppm-rendered tests). page_script runs
 * once per page, after layout is already finalized, so it can never
 * collide with where a table decided to break.
 *
 * Callers must reserve matching space via `@page { margin: ... }` in
 * pdf/shared/_styles.blade.php, in the SAME pt units used here — Dompdf's
 * default 96dpi CSS-px-to-72dpi-PDF-pt conversion would otherwise make the
 * CSS margin and these raw canvas coordinates drift apart.
 */
class SchoolPdfDecorator
{
    private const PAGE_WIDTH = 612.0;
    private const MARGIN_X = 46.0;
    private const CONTENT_WIDTH = self::PAGE_WIDTH - (2 * self::MARGIN_X);

    private const COLOR_NAVY = [0.102, 0.227, 0.424];
    private const COLOR_BLUE = [0.141, 0.443, 0.639];
    private const COLOR_GREY = [0.4, 0.4, 0.4];
    private const COLOR_LIGHT_GREY = [0.6, 0.6, 0.6];

    /**
     * @param array{schoolName: string, reportTitle: string, generatedAt: \Carbon\Carbon} $meta
     */
    public static function apply(Dompdf $dompdf, array $meta): void
    {
        $logoPath = public_path('images/logo.png');

        $dompdf->getCanvas()->page_script(
            function (int $PAGE_NUM, int $PAGE_COUNT, Canvas $canvas, FontMetrics $fontMetrics) use ($meta, $logoPath) {
                if ($PAGE_NUM > 1) {
                    self::drawRunningHeader($canvas, $fontMetrics, $meta, $logoPath, $PAGE_NUM, $PAGE_COUNT);
                }
                self::drawFooter($canvas, $fontMetrics, $meta);
            }
        );
    }

    private static function drawRunningHeader(Canvas $canvas, FontMetrics $fontMetrics, array $meta, string $logoPath, int $pageNum, int $pageCount): void
    {
        $bold = $fontMetrics->getFont('DejaVu Sans', 'bold');
        $normal = $fontMetrics->getFont('DejaVu Sans', 'normal');

        $y = 16.0;

        if (file_exists($logoPath)) {
            $canvas->image($logoPath, self::MARGIN_X, $y, 20, 20);
        }

        $textX = self::MARGIN_X + 26;
        $canvas->text($textX, $y, strtoupper($meta['schoolName']), $bold, 10.5, self::COLOR_NAVY);
        $canvas->text($textX, $y + 13, $meta['reportTitle'], $normal, 8.5, self::COLOR_GREY);

        $pageLabel = "Page {$pageNum} of {$pageCount}";
        $pageLabelWidth = $fontMetrics->getTextWidth($pageLabel, $normal, 8.5);
        $canvas->text(self::PAGE_WIDTH - self::MARGIN_X - $pageLabelWidth, $y + 6, $pageLabel, $normal, 8.5, self::COLOR_GREY);

        $canvas->line(self::MARGIN_X, $y + 32, self::PAGE_WIDTH - self::MARGIN_X, $y + 32, self::COLOR_NAVY, 1.5);
    }

    private static function drawFooter(Canvas $canvas, FontMetrics $fontMetrics, array $meta): void
    {
        $bold = $fontMetrics->getFont('DejaVu Sans', 'bold');
        $normal = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $italic = $fontMetrics->getFont('DejaVu Sans', 'italic');

        $pageHeight = $canvas->get_height();
        $top = $pageHeight - 128.0;
        $left = self::MARGIN_X;
        $right = self::PAGE_WIDTH - self::MARGIN_X;

        $canvas->line($left, $top, $right, $top, self::COLOR_NAVY, 2);

        $verse = '"But Jesus said, Suffer little children, and forbid them not, to come unto me: for of such is the kingdom of heaven."';
        $verseWidth = $fontMetrics->getTextWidth($verse, $italic, 9);
        $canvas->text(($left + $right - $verseWidth) / 2, $top + 10, $verse, $italic, 9, self::COLOR_BLUE);

        $ref = 'MATTHEW 19:14';
        $refWidth = $fontMetrics->getTextWidth($ref, $bold, 8);
        $canvas->text(($left + $right - $refWidth) / 2, $top + 22, $ref, $bold, 8, self::COLOR_NAVY);

        $canvas->line($left, $top + 36, $right, $top + 36, [0.9, 0.9, 0.9], 0.5);

        $colWidth = (self::CONTENT_WIDTH - 20) / 2;
        $leftColX = $left;
        $rightColX = $left + $colWidth + 20;
        $textY = $top + 46;

        $canvas->text($leftColX, $textY, 'VISION', $bold, 8, self::COLOR_NAVY);
        $canvas->text($rightColX, $textY, 'MISSION', $bold, 8, self::COLOR_NAVY);

        $visionLines = self::wrapText(
            "Creating and sustaining an integrated, wholesome, and appropriate environment for all phases of the learner's growth and development.",
            $normal, 8, $colWidth, $fontMetrics
        );
        $missionLines = self::wrapText(
            "The IEMELIF Learning Center is a future-oriented school which gives opportunities to all children to discover their interests and God-given talents, which will be explored and developed as they grow up and become successful individuals.",
            $normal, 8, $colWidth, $fontMetrics
        );

        $lineHeight = 10.5;
        foreach ($visionLines as $i => $line) {
            $canvas->text($leftColX, $textY + 11 + ($i * $lineHeight), $line, $normal, 8, self::COLOR_GREY);
        }
        foreach ($missionLines as $i => $line) {
            $canvas->text($rightColX, $textY + 11 + ($i * $lineHeight), $line, $normal, 8, self::COLOR_GREY);
        }

        $metaY = $top + 100;
        $canvas->line($left, $metaY, $right, $metaY, [0.94, 0.94, 0.94], 0.5);
        $canvas->text($left, $metaY + 6, $meta['schoolName'] . " \u{2014} Official Report", $normal, 8, self::COLOR_LIGHT_GREY);

        $printed = 'Printed: ' . $meta['generatedAt']->format('F d, Y');
        $printedWidth = $fontMetrics->getTextWidth($printed, $normal, 8);
        $canvas->text($right - $printedWidth, $metaY + 6, $printed, $normal, 8, self::COLOR_LIGHT_GREY);
    }

    /** @return string[] */
    private static function wrapText(string $text, string $font, float $size, float $maxWidth, FontMetrics $fontMetrics): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if ($current !== '' && $fontMetrics->getTextWidth($candidate, $font, $size) > $maxWidth) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }
}
