@if ($paginator->hasPages())
<nav class="flex items-center gap-1 flex-wrap justify-center">

    {{-- Nút trước --}}
    @if ($paginator->onFirstPage())
        <span class="page-btn disabled">&#8249;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">&#8249;</a>
    @endif

    {{-- Số trang --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Nút sau --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">&#8250;</a>
    @else
        <span class="page-btn disabled">&#8250;</span>
    @endif

</nav>
@endif