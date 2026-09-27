@extends('client.layouts.master')

@section('title', 'Chọn phim ngẫu nhiên — MINI CINE')
@section('description', 'Khám phá 6 phim được chọn ngẫu nhiên từ các phim được đánh giá tốt hoặc có nhiều lượt xem.')

@push('styles')
<style>
    .random-movie-overlay{position:fixed;inset:0;z-index:10000;display:grid;place-items:center;padding:24px;background:rgba(4,6,12,.82);backdrop-filter:blur(9px)}
    .random-movie-modal{position:relative;width:min(1120px,100%);max-height:min(90vh,900px);overflow:auto;padding:clamp(22px,4vw,42px);border:1px solid rgba(255,255,255,.14);border-radius:22px;background:#11131b;color:#f8fafc;box-shadow:0 28px 100px rgba(0,0,0,.55)}
    .random-movie-close{position:absolute;top:18px;right:18px;width:42px;height:42px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.16);border-radius:50%;background:#20232d;color:#fff;font-size:24px;text-decoration:none}
    .random-movie-heading{padding-right:52px;margin-bottom:24px}.random-movie-heading h1{margin:0 0 8px;font-size:clamp(24px,4vw,36px)}.random-movie-heading p{margin:0;color:#a9adbb}
    .random-movie-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px}
    .random-movie-card{min-width:0;color:inherit;text-decoration:none}.random-movie-poster{position:relative;overflow:hidden;aspect-ratio:2/3;border:1px solid rgba(255,255,255,.12);border-radius:13px;background:#20232d}.random-movie-poster img{width:100%;height:100%;object-fit:cover;transition:transform .25s}.random-movie-card:hover .random-movie-poster img{transform:scale(1.05)}
    .random-movie-badge{position:absolute;left:8px;top:8px;padding:5px 8px;border-radius:999px;background:rgba(109,40,217,.95);font-size:10px;font-weight:700}.random-movie-card h2{overflow:hidden;margin:10px 0 4px;font-size:14px;line-height:1.45;text-overflow:ellipsis;white-space:nowrap}.random-movie-meta{color:#a9adbb;font-size:12px}.random-movie-actions{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:28px}.random-movie-action{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 18px;border:1px solid rgba(255,255,255,.17);border-radius:999px;background:#20232d;color:#fff;text-decoration:none;font-weight:700}.random-movie-action.primary{border-color:#7c3aed;background:#6d28d9}.random-movie-empty{padding:42px 12px;text-align:center;color:#c5c8d2}
    @media(max-width:900px){.random-movie-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:520px){.random-movie-overlay{padding:10px}.random-movie-modal{padding:22px 14px;border-radius:16px}.random-movie-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.random-movie-close{top:12px;right:12px}}
</style>
@endpush

@section('content')
<div class="random-movie-overlay" id="random-movie-overlay" role="presentation">
    <section class="random-movie-modal" role="dialog" aria-modal="true" aria-labelledby="random-movie-title">
        <a class="random-movie-close" href="{{ route('home') }}" aria-label="Đóng">&times;</a>

        <header class="random-movie-heading">
            <h1 id="random-movie-title">Hôm nay xem phim gì?</h1>
            <p>6 lựa chọn ngẫu nhiên từ những phim được đánh giá tốt hoặc có nhiều lượt xem.</p>
        </header>

        @if($movies->isNotEmpty())
            <div class="random-movie-grid">
                @foreach($movies as $movie)
                    <a class="random-movie-card" href="{{ route('movie.detail', $movie->slug) }}">
                        <div class="random-movie-poster">
                            <img src="{{ $movie->poster ? asset('storage/' . $movie->poster) : asset('images/poster-default.png') }}" alt="Áp phích phim {{ $movie->title }}" loading="lazy">
                            @if(($movie->ratings_avg_rating ?? 0) >= 4)
                                <span class="random-movie-badge">Đánh giá tốt</span>
                            @else
                                <span class="random-movie-badge">Xem nhiều</span>
                            @endif
                        </div>
                        <h2>{{ $movie->title }}</h2>
                        <div class="random-movie-meta">
                            {{ $movie->genres->first()->name ?? 'Phim' }}
                            @if($movie->release_year) · {{ $movie->release_year }} @endif
                            · {{ number_format($movie->views_count) }} lượt xem
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="random-movie-empty">Hiện chưa có phim được xuất bản để gợi ý.</div>
        @endif

        <div class="random-movie-actions">
            <a class="random-movie-action primary" href="{{ route('movies.random', ['refresh' => now()->timestamp]) }}">↻ Chọn 6 phim khác</a>
            <a class="random-movie-action" href="{{ route('home') }}">Về trang chủ</a>
        </div>
    </section>
</div>

<script>
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') window.location.href = @json(route('home'));
    });
</script>
@endsection
