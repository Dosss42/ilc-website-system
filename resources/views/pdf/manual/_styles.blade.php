{{-- Shared styles for the system User Manual PDF — a long, book-style
     document, deliberately NOT reusing pdf.shared._styles/_bottom (that
     design's repeating Bible-verse/Vision-Mission footer on every page
     fits a formal financial report, not an 80-page software manual).
     Branding (navy blue palette, DejaVu Sans, logo) stays consistent with
     the rest of the system; the page-decoration mechanism is a plain page
     number via Canvas::page_text(), wired in App\Support\ManualPdfDecorator.

     Margin: @page{margin} + an explicit html{margin} override — the
     html{} line is NOT redundant, same reason documented in
     pdf/shared/_styles.blade.php: Dompdf implements @page margin by
     assigning it as the ROOT <html> frame's own `margin` property, and the
     universal `* { margin: 0 }` reset below matches <html> too, silently
     stomping that value back to 0 by normal cascade rules unless a later,
     higher-specificity type-selector rule restores it. --}}
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    @page { margin: 60pt 54pt 50pt 54pt; }
    html { margin: 60pt 54pt 50pt 54pt; }

    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #222; line-height: 1.5; }

    .cover { text-align: center; padding-top: 140px; page-break-after: always; }
    .cover img { width: 90px; height: 90px; object-fit: contain; }
    .cover .school-name { font-size: 24px; font-weight: bold; color: #1a3a6c; margin-top: 16px; }
    .cover .school-addr { font-size: 11px; color: #666; margin-top: 4px; }
    .cover .manual-title { font-size: 20px; font-weight: bold; color: #2471a3; text-transform: uppercase; letter-spacing: 2px; margin-top: 70px; }
    .cover .manual-sub { font-size: 12px; color: #666; margin-top: 10px; }
    .cover .manual-meta { font-size: 10px; color: #999; margin-top: 100px; }

    .toc-title { font-size: 18px; font-weight: bold; color: #1a3a6c; margin-bottom: 18px; }
    .toc-entry { font-size: 11px; padding: 5px 0; border-bottom: 1px solid #eee; }
    .toc-entry .toc-num { color: #2471a3; font-weight: bold; display: inline-block; width: 24px; }

    {{-- page-break-before:always on every .chapter div (including the
         first) is the ONLY break between the TOC and the Introduction —
         .toc itself must NOT also carry page-break-after:always, or the
         two rules stack into a wasted blank page (confirmed by rendering:
         .chapter:first-of-type never matched anything in the first place,
         since the .cover/.toc divs share the same `div` tag and sit ahead
         of it in sibling order, so this was dead weight either way). --}}
    .chapter { page-break-before: always; }
    h1.chapter-title { font-size: 17px; font-weight: bold; color: #fff; background: #1a3a6c; padding: 10px 14px; margin-bottom: 14px; }
    h2.section-title { font-size: 12.5px; font-weight: bold; color: #1a3a6c; margin: 16px 0 6px; padding-bottom: 3px; border-bottom: 1.5px solid #2471a3; }
    h3.sub-title { font-size: 11px; font-weight: bold; color: #333; margin: 10px 0 4px; }

    p { margin-bottom: 8px; }
    ol, ul { margin: 6px 0 10px 20px; }
    ol li, ul li { margin-bottom: 4px; }

    .note-box { background: #fff8ec; border-left: 3px solid #f5a623; padding: 8px 12px; margin: 10px 0; font-size: 9.5px; color: #555; }
    .sidebar-path { display: inline-block; background: #f0f4ff; color: #1a3a6c; font-weight: bold; padding: 1px 6px; border-radius: 3px; font-size: 9.5px; }

    {{-- Chapter content is converted from Markdown (via league/commonmark)
         to keep the source content readable to write/review — blockquotes
         (> **Tip:**/**Note:**/**Warning:**) are styled to match .note-box
         rather than post-processed into divs, simpler and just as reliable. --}}
    blockquote { background: #fff8ec; border-left: 3px solid #f5a623; padding: 8px 12px; margin: 10px 0; font-size: 9.5px; color: #555; }
    blockquote p { margin: 0; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 9.5px; }
    table th { background: #1a3a6c; color: #fff; padding: 5px 8px; text-align: left; }
    table td { border: 1px solid #ddd; padding: 5px 8px; vertical-align: top; }
    table tr:nth-child(even) td { background: #f7f9fc; }
    code { background: #f0f4ff; color: #1a3a6c; padding: 1px 4px; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 9px; }
</style>
