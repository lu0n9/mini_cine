@extends('admin.layouts.master')

@section('content')

<section id="featured" class="page">

    <div class="page-head">

        <div>
            <h3>Phim nổi bật</h3>

            <p>
                Các phim được đánh dấu Featured hiển thị ở khu vực nổi bật trang chủ.
            </p>
        </div>

    </div>


    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert error">
            {{ session('error') }}
        </div>
    @endif


    <div class="poster-grid">

        @forelse($movies as $movie)

            <div class="poster">

                <div class="pic">

                    <span class="rk">
                        Featured
                    </span>

                    <img
                        src="{{ asset('Storage/' . $movie->poster) }}"
                        alt="{{ $movie->title }}"
                    >

                </div>


                <div class="meta">

                    <h5>
                        {{ $movie->title }}
                    </h5>

                    <div class="sub">

                        <span>
                            {{ $movie->release_year ?? $movie->created_at->format('Y') }}
                        </span>

                        <span>
                            ★ {{ number_format($movie->rating ?? 0, 1) }}
                        </span>

                    </div>


                    {{-- Hủy nổi bật --}}
                    <form
                        action="{{ route('admin.popular.unpopular', $movie) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn hủy phim này khỏi danh sách nổi bật?')"
                        style="margin-top: 10px;"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn ghost"
                            style="width: 100%;"
                        >
                            Hủy nổi bật
                        </button>

                    </form>

                </div>

            </div>

        @empty

            <div style="grid-column: 1 / -1; padding: 40px; text-align: center;">

                <p>
                    Chưa có phim nổi bật nào.
                </p>

            </div>

        @endforelse

    </div>

</section>

@endsection