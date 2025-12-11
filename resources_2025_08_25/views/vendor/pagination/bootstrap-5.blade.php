@if ($paginator->hasPages())
<div class="footer-pagination d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="pagination-info">
        <p class="mb-0">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </p>
    </div>


    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item previous disabled">
            <span class="page-link"><i class="previous"></i></span>
        </li>
        @else
        <li class="page-item previous">
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link"><i class="previous"></i></a>
        </li>
        @endif

        {{-- Pagination Links for More than 7 Pages --}}
        @if ($paginator->lastPage() > 7)

        {{-- First Page Link --}}

        @if ($paginator->currentPage() === 1)
        <li class="page-item active">
            <a href="{{ $paginator->url(1) }}" class="page-link">1</a>
        </li>
        @else
        <li class="page-item">
            <a href="{{ $paginator->url(1) }}" class="page-link">1</a>
        </li>
        @endif

        {{-- Left Ellipses --}}
        @if ($paginator->currentPage() > 4)
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>
        @endif

        {{-- Pagination Links --}}
        {{-- This code is showing page 1 twice, because it has been rendered on top already therefore range should start from 2 instead of 1 --}}
        {{-- @foreach (range(1, $paginator->lastPage()) as $page) --}}

        @foreach (range(2, $paginator->lastPage()) as $page)
        @if (
        ($page >= $paginator->currentPage() - 1 && $page <= $paginator->currentPage() + 1) || {{-- Current page and neighbors --}}
            ($paginator->currentPage() <= 4 && $page <=5) || {{-- First 5 pages --}}
                ($paginator->currentPage() >= $paginator->lastPage() - 3 && $page >= $paginator->lastPage() - 4) {{-- Last 5 pages --}}
                )
                @if ($page == $paginator->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
                @else
                <li class="page-item">
                    <a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a>
                </li>
                @endif
                @endif
                @endforeach

                {{-- Right Ellipses --}}
                @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    @endif

                    {{-- Last Page Link --}}
                    @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                        <li class="page-item">
                            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link">{{ $paginator->lastPage() }}</a>
                        </li>
                        @endif
                        @else
                        {{-- Pagination Links for 7 or Fewer Pages --}}
                        @foreach (range(1, $paginator->lastPage()) as $page)
                        @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a>
                        </li>
                        @endif
                        @endforeach
                        @endif
                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                        <li class="page-item next">
                            <a href="{{ $paginator->nextPageUrl() }}" class="page-link"><i class="next"></i></a>
                        </li>
                        @else
                        <li class="page-item next disabled">
                            <span class="page-link"><i class="next"></i></span>
                        </li>
                        @endif
    </ul>


</div>
@endif
