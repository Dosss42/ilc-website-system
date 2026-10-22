/*
 * Which page numbers a pager shows — at most 10 numbered buttons, everywhere.
 * Same rule as resources/views/partials/pagination.blade.php (server-rendered
 * pagers), so AJAX tables and Blade tables page identically:
 *   last <= 10       → every page:            1 2 3 4 5 6 7
 *   near the start   → 1 2 3 4 5 6 7 8 9 … 17
 *   in the middle    → 1 … 5 6 7 8 9 10 11 12 … 17
 *   near the end     → 1 … 9 10 11 12 13 14 15 16 17
 * Returns numbers and the string '…' for gaps.
 */
function pageWindow(current, last, max) {
    max = max || 10;
    current = Math.min(Math.max(1, parseInt(current, 10) || 1), Math.max(1, last));
    var pages = [], i;
    if (last <= max) {
        for (i = 1; i <= last; i++) pages.push(i);
        return pages;
    }
    var inner = max - 2;                          // slots between page 1 and the last page
    var start = current - Math.floor((inner - 1) / 2);
    var end = start + inner - 1;
    if (start < 2) { start = 2; end = start + inner - 1; }
    if (end > last - 1) { end = last - 1; start = end - inner + 1; }

    pages.push(1);
    if (start > 2) pages.push('…');
    for (i = start; i <= end; i++) pages.push(i);
    if (end < last - 1) pages.push('…');
    pages.push(last);
    return pages;
}
