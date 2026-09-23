@extends('client.layouts.master')

@section('content')
    <main>
        <section
            class="section"
            id="moi-cap-nhat"
            aria-labelledby="moi-title">
            <div class="shell">
                <div class="genre-filter-minimal">
                <form method="GET" action="{{ route('genres.show', $genre->slug) }}" class="minimal-form">
                    <div class="filter-field">
                        <label>Thể loại</label>
                        <select name="genre" class="custom-select-min" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($genres as $item)
                                <option value="{{ $item->id }}" @selected((string) request('genre') === (string) $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Quốc gia</label>
                        <select name="country" class="custom-select-min" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" @selected((string) request('country') === (string) $country->id)>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Loại phim</label>
                        <select name="type" class="custom-select-min" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected((string) request('type') === (string) $type)>
                                    {{ $type}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Năm</label>
                        <select name="year" class="custom-select-min" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Điểm IMDb</label>
                        <select name="imdb" class="custom-select-min" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="8" @selected(request('imdb') == '8')>Từ 8.0</option>
                            <option value="7" @selected(request('imdb') == '7')>Từ 7.0</option>
                            <option value="6" @selected(request('imdb') == '6')>Từ 6.0</option>
                            <option value="5" @selected(request('imdb') == '5')>Từ 5.0</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label>Sắp xếp</label>
                        <select name="sort" class="custom-select-min" onchange="this.form.submit()">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                            <option value="rating" @selected(request('sort') === 'rating')>IMDb cao nhất</option>
                            <option value="views" @selected(request('sort') === 'views')>Xem nhiều nhất</option>
                            <option value="name" @selected(request('sort') === 'name')>Tên A-Z</option>
                        </select>
                    </div>
                </form>
            </div>

                <ul class="grid">
                    @forelse($movies as $movie)
                        <li class="card">
                            <a
                                href="{{ route('movie.detail', $movie->slug) }}"
                            >
                                <div class="poster">
                                    <img
                                        src="{{ $movie->poster
                                            ? asset('storage/' . $movie->poster)
                                            : asset('images/poster-default.png') }}"
                                        alt="Áp phích phim {{ $movie->title }}"
                                        width="336"
                                        height="504"
                                    >

                                    @if($movie->type === 'series')
                                        <span class="poster__badge">
                                            {{ $movie->episodes->count() }} Tập
                                        </span>
                                    @elseif($movie->created_at->isToday())
                                        <span class="poster__badge">
                                            Mới
                                        </span>
                                    @elseif($movie->quality === '4K')
                                        <span class="poster__badge">
                                            4K
                                        </span>
                                    @endif
                                </div>

                                <p class="card__title">
                                    {{ $movie->title }}
                                </p>

                                <p class="card__sub">
                                    {{ $movie->release_year ?? '—' }}
                                    ·
                                    {{ $movie->language === 'vietsub'
                                        ? 'Phụ đề Việt'
                                        : ($movie->language === 'thuyet_minh'
                                            ? 'Thuyết minh'
                                            : 'Lồng tiếng') }}
                                </p>
                            </a>
                        </li>
                    @empty
                        <li>
                            Không tìm thấy phim phù hợp.
                        </li>
                    @endforelse
                </ul>

                <div class="pagination">
                    {{ $movies->links() }}
                </div>

            </div>
        </section>
    </main>
@endsection