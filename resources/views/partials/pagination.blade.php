@if ($paginator->hasPages())
    @php $pgBase = 'px-space-sm py-space-xs rounded-lg border border-outline-variant font-label-lg text-label-lg'; @endphp
    <div class="px-space-lg py-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-md text-body-md text-on-surface-variant">
        <div>Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }}</div>
        <div class="flex gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="{{ $pgBase }} opacity-50">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">‹</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="{{ $pgBase }} bg-primary text-on-primary border-primary">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="{{ $pgBase }} hover:bg-surface-container">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">›</a>
            @else
                <span class="{{ $pgBase }} opacity-50">›</span>
            @endif
        </div>
    </div>
@endif