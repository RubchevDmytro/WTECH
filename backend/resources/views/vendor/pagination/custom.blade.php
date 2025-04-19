@if ($paginator->hasPages())
    <nav class="pagination-nav">
        <ul class="pagination-list">
            {{-- Кнопка "Предыдущая" --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link">«</span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">«</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination-item disabled">
                        <span class="pagination-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active">
                                <span class="pagination-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">»</a>
                </li>
            @else
                <li class="pagination-item disabled">
                    <span class="pagination-link">»</span>
                </li>
            @endif
        </ul>

        {{-- Информация о текущей странице --}}
        <div class="pagination-info">
            Page: {{ $paginator->currentPage() }} from {{ $paginator->lastPage() }}
            ({{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} from
            {{ $paginator->total() }} products )
        </div>
    </nav>
@endif
