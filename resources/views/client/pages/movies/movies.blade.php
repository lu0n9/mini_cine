@extends('client.layouts.master')

@section('title', 'Danh sách phim — MINI CINE')
@section('description', 'Khám phá và lọc phim theo thể loại, quốc gia, năm phát hành, ngôn ngữ và điểm IMDb.')

@push('styles')
<style>
    .movies-page{max-width:1280px;margin:0 auto}.movies-heading{display:flex;justify-content:space-between;align-items:end;gap:16px;margin:8px 0 24px}.movies-heading h1{margin:0 0 6px;font-size:clamp(28px,4vw,40px)}.movies-heading p{margin:0;color:#a9adbb}.movies-total{white-space:nowrap;color:#a9adbb}
    .movies-filter{display:grid;grid-template-columns:minmax(220px,2fr) repeat(3,minmax(145px,1fr));gap:12px;padding:18px;margin-bottom:24px;border:1px solid rgba(255,255,255,.1);border-radius:14px;background:rgba(255,255,255,.025)}.movies-filter input,.movies-filter select{width:100%;min-height:44px;padding:0 12px;border:1px solid rgba(255,255,255,.14);border-radius:9px;background:#151720;color:#f8fafc;font:inherit}.movies-filter select option{background:#151720}.movies-filter-actions{grid-column:1/-1;display:flex;align-items:center;gap:10px}.movies-filter-actions button,.movies-filter-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 16px;border:1px solid rgba(255,255,255,.16);border-radius:999px;background:#6d28d9;color:#fff;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.movies-filter-actions a{background:#20232d;color:#d6d8df}.movies-empty{grid-column:1/-1;padding:45px 16px;text-align:center;color:#b5b8c4}.movies-rating{position:absolute;right:8px;top:8px;padding:4px 7px;border-radius:6px;background:rgba(0,0,0,.78);font-size:12px;font-weight:700}.movies-page .card>a{text-decoration:none;color:inherit}.movies-page .card__sub{color:#a9adbb}
    @media(max-width:900px){.movies-filter{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.movies-heading{align-items:start;flex-direction:column}.movies-filter{grid-template-columns:1fr;padding:14px}.movies-filter-actions{grid-column:auto}}
</style>
@endpush

@section('content')
<section class="movies-page section" aria-labelledby="movies-title">
    <header class="movies-heading">
        <div>
            <h1 id="movies-title">Khám phá phim</h1>
            <p>Tìm bộ phim phù hợp với bạn trong kho phim MINI CINE.</p>
        </div>
        <span class="movies-total">{{ number_format($movies->total()) }} phim</span>
    </header>

    <form class="movies-filter" method="GET" action="{{ route('movies.index') }}">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm tên phim..." aria-label="Tìm tên phim">

        <select name="type" aria-label="Loại phim">
            <option value="">Tất cả loại phim</option>
            @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>
                    {{ $type === 'single' ? 'Phim lẻ' : ($type === 'series' ? 'Phim bộ' : $type) }}
                </option>
            @endforeach
        </select>

        <select name="genre" aria-label="Thể loại">
            <option value="">Tất cả thể loại</option>
            @foreach($genres as $genre)
                <option value="{{ $genre->id }}" @selected((string) request('genre') === (string) $genre->id)>{{ $genre->name }}</option>
            @endforeach
        </select>

        <select name="country" aria-label="Quốc gia">
            <option value="">Tất cả quốc gia</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" @selected((string) request('country') === (string) $country->id)>{{ $country->name }}</option>
            @endforeach
        </select>

        <select name="year" aria-label="Năm phát hành">
            <option value="">Mọi năm</option>
            @foreach($years as $year)
                <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
            @endforeach
        </select>

        <select name="language" aria-label="Ngôn ngữ">
            <option value="">Mọi ngôn ngữ</option>
            @foreach($languages as $language)
                <option value="{{ $language }}" @selected(request('language') === $language)>
                    {{ $language === 'vietsub' ? 'Phụ đề Việt' : ($language === 'thuyet_minh' ? 'Thuyết minh' : ($language === 'long_tieng' ? 'Lồng tiếng' : $language)) }}
                </option>
            @endforeach
        </select>

        <select name="rating" aria-label="Điểm IMDb tối thiểu">
            <option value="">Mọi mức IMDb</option>
            @foreach([8, 7, 6, 5] as $minimumRating)
                <option value="{{ $minimumRating }}" @selected((string) request('rating') === (string) $minimumRating)>IMDb từ {{ number_format($minimumRating, 1) }}</option>
            @endforeach
        </select>

        <select name="sort" aria-label="Sắp xếp phim">
            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới cập nhật</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
            <option value="rating" @selected(request('sort') === 'rating')>IMDb cao nhất</option>
            <option value="views" @selected(request('sort') === 'views')>Xem nhiều nhất</option>
            <option value="name" @selected(request('sort') === 'name')>Tên A–Z</option>
        </select>

        <div class="movies-filter-actions">
            <button type="submit">Lọc phim</button>
            <a href="{{ route('movies.index') }}">Xóa bộ lọc</a>
        </div>
    </form>

    <ul class="grid">
        @forelse($movies as $movie)
            <li class="card">
                <a href="{{ route('movie.detail', $movie->slug) }}">
                    <div class="poster">
                        <img src="{{ $movie->poster ? asset('storage/' . $movie->poster) : asset('images/poster-default.png') }}" alt="Áp phích phim {{ $movie->title }}" width="336" height="504" loading="lazy">
                        @if($movie->imdb_rating)
                            <span class="movies-rating">★ {{ number_format($movie->imdb_rating, 1) }}</span>
                        @endif
                        @if($movie->type === 'series')
                            <span class="poster__badge">{{ $movie->episodes_count }} tập</span>
                        @elseif($movie->created_at?->isToday())
                            <span class="poster__badge">Mới</span>
                        @elseif($movie->quality === '4K')
                            <span class="poster__badge">4K</span>
                        @endif
                    </div>
                    <p class="card__title">{{ $movie->title }}</p>
                    <p class="card__sub">
                        {{ $movie->release_year ?? '—' }}
                        · {{ $movie->genres->first()->name ?? 'Phim' }}
                        · {{ $movie->language === 'vietsub' ? 'Phụ đề Việt' : ($movie->language === 'thuyet_minh' ? 'Thuyết minh' : ($movie->language === 'long_tieng' ? 'Lồng tiếng' : $movie->language)) }}
                    </p>
                </a>
            </li>
        @empty
            <li class="movies-empty">Không tìm thấy phim phù hợp với bộ lọc.</li>
        @endforelse
    </ul>

    @if($movies->hasPages())
        <div class="pagination">{{ $movies->links('pagination::custom') }}</div>
    @endif
</section>
@endsection
