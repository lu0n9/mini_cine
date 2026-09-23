@extends('admin.layouts.master')

@section('content')

<section id="views" class="page">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="page-head">
        <div>
            <h3>Movie Views</h3>
            <p>
                Thống kê lượt xem phim trên hệ thống Mini Cine.
            </p>
        </div>
    </div>


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}
    <div class="stats">

        {{-- Tổng lượt xem --}}
        <div class="stat">
            <span class="label">Tổng lượt xem</span>

            <div class="num">
                {{ number_format($totalViews) }}
            </div>
        </div>


        {{-- Hôm nay --}}
        <div class="stat">
            <span class="label">Hôm nay</span>

            <div class="num">
                {{ number_format($todayViews) }}
            </div>
        </div>


        {{-- Tuần này --}}
        <div class="stat">
            <span class="label">Tuần này</span>

            <div class="num">
                {{ number_format($weekViews) }}
            </div>
        </div>


        {{-- Tháng này --}}
        <div class="stat">
            <span class="label">Tháng này</span>

            <div class="num">
                {{ number_format($monthViews) }}
            </div>
        </div>

    </div>

    {{-- =========================================================
        TOTAL VIEWS BY MOVIE
    ========================================================== --}}
    <div class="panel" style="margin-top:20px;">

        <div class="page-head" style="margin-bottom:20px;">

            <div>
                <h3>Lượt xem theo phim</h3>

                <p>
                    Tổng số lượt xem của từng phim trên hệ thống.
                </p>
            </div>

        </div>


        <table>

            <thead>

                <tr>

                    <th style="width:70px;">
                        #
                    </th>

                    <th>
                        Phim
                    </th>

                    <th style="width:180px;">
                        Tổng lượt xem
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($movieViews as $index => $movie)

                    <tr>

                        {{-- STT --}}
                        <td>

                            {{ $movieViews->firstItem() + $index }}

                        </td>


                        {{-- Movie --}}
                        <td>

                            <div style="
                                display:flex;
                                align-items:center;
                                gap:12px;
                            ">

                                {{-- Poster --}}
                                @if($movie->poster)

                                    <img
                                        src="{{ asset('storage/' . $movie->poster) }}"
                                        alt="{{ $movie->title }}"
                                        style="
                                            width:42px;
                                            height:60px;
                                            object-fit:cover;
                                            border-radius:6px;
                                            display:block;
                                            flex-shrink:0;
                                        "
                                    >

                                @else

                                    <div style="
                                        width:42px;
                                        height:60px;
                                        border-radius:6px;
                                        background:#eee;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:9px;
                                        color:#777;
                                        flex-shrink:0;
                                    ">
                                        No image
                                    </div>

                                @endif


                                {{-- Movie title --}}
                                <div>

                                    <div style="
                                        font-weight:600;
                                        line-height:1.4;
                                    ">
                                        {{ $movie->title }}
                                    </div>

                                    @if($movie->slug)

                                        <div style="
                                            margin-top:3px;
                                            font-size:12px;
                                            color:#888;
                                        ">
                                            {{ $movie->slug }}
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- Total views --}}
                        <td>

                            <strong>
                                {{ number_format($movie->views_count) }}
                            </strong>

                            <span style="
                                margin-left:4px;
                                font-size:12px;
                                color:#888;
                            ">
                                lượt
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            style="
                                text-align:center;
                                padding:30px;
                                color:#777;
                            "
                        >
                            Chưa có dữ liệu lượt xem.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>


        {{-- Pagination --}}
        @if($movieViews->hasPages())

            <div style="margin-top:20px;">

                {{ $movieViews->links('pagination::custom') }}

            </div>

        @endif

    </div>

</section>

@endsection