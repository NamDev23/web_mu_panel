@if ($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <span>Hiển thị {{ $paginator->firstItem() }} đến {{ $paginator->lastItem() }} trong tổng số {{ $paginator->total() }} kết quả</span>
        </div>
        <div class="pagination-nav">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-link disabled">‹ Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link">‹ Trước</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-link disabled">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link">Sau ›</a>
            @else
                <span class="page-link disabled">Sau ›</span>
            @endif
        </div>
    </div>

    <style>
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.05);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .pagination-info {
            color: white;
            font-size: 14px;
            opacity: 0.8;
        }
        
        .pagination-nav {
            display: flex;
            gap: 8px;
        }
        
        .page-link {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            min-width: 40px;
            text-align: center;
        }
        
        .page-link:hover:not(.disabled):not(.active) {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }
        
        .page-link.active {
            background: linear-gradient(45deg, #3b82f6, #2563eb);
            border-color: #3b82f6;
            color: white;
            font-weight: 600;
        }
        
        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: rgba(255, 255, 255, 0.05);
        }
        
        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .pagination-nav {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
@endif
