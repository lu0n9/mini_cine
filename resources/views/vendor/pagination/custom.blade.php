@if ($paginator->hasPages())
    <nav class="mini-pagination" aria-label="Pagination">

        <ul class="pagination mb-0">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link pagination-arrow">
                        &lsaquo;
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="page-link pagination-arrow"
                       rel="prev"
                       aria-label="Trang trước">
                        &lsaquo;
                    </a>
                </li>
            @endif


            {{-- Pagination Elements --}}
            @foreach ($elements as $element)

                {{-- Dấu ... --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link pagination-dots">
                            {{ $element }}
                        </span>
                    </li>
                @endif


                {{-- Page numbers --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        @if ($page == $paginator->currentPage())

                            <li class="page-item"
                                aria-current="page">

                                <span class="page-link pagination-number active-page">
                                    {{ $page }}
                                </span>

                            </li>

                        @else

                            <li class="page-item">
                                <a href="{{ $url }}"
                                   class="page-link pagination-number">
                                    {{ $page }}
                                </a>
                            </li>

                        @endif

                    @endforeach
                @endif

            @endforeach


            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="page-link pagination-arrow"
                       rel="next"
                       aria-label="Trang sau">
                        &rsaquo;
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link pagination-arrow">
                        &rsaquo;
                    </span>
                </li>
            @endif

        </ul>

    </nav>
@endif


<style>
    /* =========================================
       MINI CINE - MINIMAL PAGINATION
    ========================================= */

    .mini-pagination {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;

        padding: 25px 0;

        /* Quan trọng: tránh CSS bên ngoài ảnh hưởng */
        list-style: none !important;
    }


    /* =========================================
       RESET BOOTSTRAP + CSS BÊN NGOÀI
    ========================================= */

    .mini-pagination ul,
    .mini-pagination ol,
    .mini-pagination li {
        list-style: none !important;

        margin: 0 !important;
        padding: 0 !important;
    }

    /*
     * XÓA HOÀN TOÀN các dấu chấm được tạo
     * bởi ::before / ::after / ::marker
     */
    .mini-pagination *,
    .mini-pagination *::before,
    .mini-pagination *::after {
        box-sizing: border-box;
    }

    .mini-pagination .page-item::before,
    .mini-pagination .page-item::after,
    .mini-pagination .page-link::before,
    .mini-pagination .page-link::after,
    .mini-pagination .pagination::before,
    .mini-pagination .pagination::after {
        content: none !important;
        display: none !important;
    }

    .mini-pagination .page-item::marker,
    .mini-pagination .page-link::marker {
        content: none !important;
        display: none !important;
    }


    /* =========================================
       PAGINATION CONTAINER
    ========================================= */

    .mini-pagination .pagination {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        width: auto !important;

        gap: 2px !important;

        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }


    /* =========================================
       PAGE ITEM
    ========================================= */

    .mini-pagination .page-item {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        background: transparent !important;
        border: none !important;
    }


    /* =========================================
       PAGE LINK RESET
    ========================================= */

    .mini-pagination .page-link {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;

        text-decoration: none !important;

        background: transparent !important;
    }


    /* =========================================
       PAGE NUMBER
    ========================================= */

    .mini-pagination .pagination-number {
        width: 38px !important;
        height: 38px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        padding: 0 !important;
        margin: 0 !important;

        color: #555 !important;
        background: transparent !important;

        border-radius: 50% !important;

        font-size: 14px !important;
        font-weight: 500 !important;

        transition:
            color 0.2s ease,
            background-color 0.2s ease;
    }


    /* Hover */

    .mini-pagination .pagination-number:hover {
        color: #111 !important;
        background: #f2f2f2 !important;
    }


    /* =========================================
       CURRENT PAGE
    ========================================= */

    .mini-pagination .active-page {
        color: #fff !important;
        background: #111 !important;

        font-weight: 600 !important;

        cursor: default;
    }

    .mini-pagination .active-page:hover {
        color: #fff !important;
        background: #111 !important;
    }


    /* =========================================
       ARROWS
    ========================================= */

    .mini-pagination .pagination-arrow {
        width: 38px !important;
        height: 38px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        padding: 0 !important;
        margin: 0 !important;

        color: #555 !important;
        background: transparent !important;

        border-radius: 50% !important;

        font-size: 25px !important;
        font-weight: 300 !important;

        line-height: 1 !important;

        transition:
            color 0.2s ease,
            background-color 0.2s ease;
    }


    /* Arrow hover */

    .mini-pagination .pagination-arrow:hover {
        color: #111 !important;
        background: #f2f2f2 !important;
    }


    /* Arrow disabled */

    .mini-pagination .page-item.disabled .pagination-arrow {
        color: #d0d0d0 !important;
        background: transparent !important;

        cursor: default;
    }


    /* =========================================
       THREE DOTS (...) CỦA LARAVEL
    ========================================= */

    .mini-pagination .pagination-dots {
        width: 30px !important;
        height: 38px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        padding: 0 !important;
        margin: 0 !important;

        color: #999 !important;
        background: transparent !important;

        font-size: 14px !important;
    }


    /* =========================================
       MOBILE
    ========================================= */

    @media (max-width: 575.98px) {

        .mini-pagination {
            padding: 20px 0;
        }

        .mini-pagination .pagination {
            gap: 1px !important;
        }

        .mini-pagination .pagination-number,
        .mini-pagination .pagination-arrow {
            width: 34px !important;
            height: 34px !important;
        }

        .mini-pagination .pagination-number {
            font-size: 13px !important;
        }

        .mini-pagination .pagination-arrow {
            font-size: 22px !important;
        }
    }
</style>