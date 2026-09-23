@extends('admin.layouts.master')

@section('content')

<section id="popular-stats" class="page">

    <div class="page-head">
        <div>
            <h3>Popular Movies</h3>
            <p>Thống kê phim theo lượt xem và yêu thích.</p>
        </div>
    </div>


    <div class="grid-2">

        {{-- TOP LƯỢT XEM --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Top lượt xem 30 ngày</h4>
            </div>

            <div class="panel-body">

                @if($topViewedMovies->count())

                    <div class="chart">

                        @foreach($topViewedMovies as $item)

                            <div class="bar-wrap">

                                <div
                                    class="bar"
                                    style="height: {{ $item->bar_height }}%;"
                                    title="{{ number_format($item->views_count) }} lượt xem"
                                ></div>

                                <span class="bar-x">
                                    {{ \Illuminate\Support\Str::limit(
                                        $item->movie?->title ?? 'Không rõ',
                                        10
                                    ) }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div style="
                        text-align:center;
                        padding:40px 20px;
                        color:#888;
                    ">
                        Chưa có dữ liệu lượt xem trong 30 ngày.
                    </div>

                @endif

            </div>

        </div>


        {{-- TOP YÊU THÍCH --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Top yêu thích</h4>
            </div>

            <div class="panel-body activity">

                @forelse($topFavoriteMovies as $index => $item)

                    <div class="act">

                        <div class="dot">
                            {{ $index + 1 }}
                        </div>

                        <div class="txt">

                            <b>
                                {{ $item->movie?->title ?? 'Phim không tồn tại' }}
                            </b>

                            <small>
                                {{ number_format($item->favorites_count) }}
                                lượt yêu thích
                            </small>

                        </div>

                    </div>

                @empty

                    <div style="
                        text-align:center;
                        padding:30px 20px;
                        color:#888;
                    ">
                        Chưa có dữ liệu yêu thích.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</section>

@endsection