{{-- Every ->links() in the app. At most 10 numbered page buttons — same rule as
     public/js/page-window.js (the AJAX-built pagers), so all pagers match:
     first + last page always shown, 8 around the current one, "…" for gaps. --}}
@php
    $pgCurrent = $paginator->currentPage();
    $pgLast    = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : $pgCurrent + ($paginator->hasMorePages() ? 1 : 0);
    $pgMax     = 10;
    if ($pgLast <= $pgMax) {
        $pgItems = range(1, max(1, $pgLast));
    } else {
        $pgInner = $pgMax - 2;
        $pgStart = $pgCurrent - intdiv($pgInner - 1, 2);
        $pgEnd   = $pgStart + $pgInner - 1;
        if ($pgStart < 2)          { $pgStart = 2; $pgEnd = $pgStart + $pgInner - 1; }
        if ($pgEnd > $pgLast - 1)  { $pgEnd = $pgLast - 1; $pgStart = $pgEnd - $pgInner + 1; }
        $pgItems = array_merge([1], $pgStart > 2 ? ['…'] : [], range($pgStart, $pgEnd), $pgEnd < $pgLast - 1 ? ['…'] : [], [$pgLast]);
    }
@endphp
<nav>
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                <span class="page-link" aria-hidden="true">Previous</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">Previous</a>
            </li>
        @endif

        {{-- Page numbers (max 10) --}}
        @foreach ($pgItems as $page)
            @if ($page === '…')
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">…</span></li>
            @elseif ($page == $pgCurrent)
                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">Next</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                <span class="page-link" aria-hidden="true">Next</span>
            </li>
        @endif
    </ul>
</nav>
