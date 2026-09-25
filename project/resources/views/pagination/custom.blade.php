@if ($paginator->hasPages())
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
        <span>
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        </span>

        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                       href="javascript:void(0)">
                        <iconify-icon icon="ep:d-arrow-left" class=""></iconify-icon>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                       href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <iconify-icon icon="ep:d-arrow-left" class=""></iconify-icon>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <a class="page-link text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md bg-primary-600 text-white"
                                   href="javascript:void(0)">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px"
                                   href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                       href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <iconify-icon icon="ep:d-arrow-right" class=""></iconify-icon>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md"
                       href="javascript:void(0)">
                        <iconify-icon icon="ep:d-arrow-right" class=""></iconify-icon>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif
