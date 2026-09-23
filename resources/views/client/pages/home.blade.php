@extends('client.layouts.master')
@section('content')
    <main>
      <!-- ================= HERO ================= -->
        @if ($proposeMovie)
            <section
                class="hero"
                aria-labelledby="hero-title">
                <div class="hero__media">
                    <img
                        src="{{ asset('Storage/' . $proposeMovie->backdrop) }}"
                        alt="{{ $proposeMovie->title }}"
                        width="1920"
                        height="820">
                </div>

                <div class="hero__body">
                    <div class="shell">
                        <div class="hero__inner">
                            <p class="eyebrow">
                                Đề xuất hôm nay
                            </p>

                            <h1 id="hero-title">
                                {{ $proposeMovie->title }}
                            </h1>

                            <div class="hero__meta">
                                <span class="score">
                                    {{ number_format($proposeMovie->imdb_rating ?? 0, 1) }} IMDb
                                </span>

                                <span
                                    class="dot"
                                    aria-hidden="true"
                                ></span>

                                <span>
                                    {{ $proposeMovie->release_year ?? '—' }}
                                </span>

                                @if ($proposeMovie->duration)
                                    <span
                                        class="dot"
                                        aria-hidden="true"
                                    ></span>

                                    <span>
                                        {{ floor($proposeMovie->duration / 60) }} giờ
                                        {{ $proposeMovie->duration % 60 }} phút
                                    </span>
                                @endif

                                @if ($proposeMovie->genres->isNotEmpty())
                                    <span
                                        class="dot"
                                        aria-hidden="true"
                                    ></span>

                                    <span>
                                        {{ $proposeMovie->genres->pluck('name')->join(' · ') }}
                                    </span>
                                @endif

                                @if ($proposeMovie->quality)
                                    <span class="tag">
                                        {{ $proposeMovie->quality }}
                                    </span>
                                @endif
                            </div>

                            <p class="hero__desc">
                                {{ $proposeMovie->short_description ?? $proposeMovie->description }}
                            </p>

                            <div class="hero__cta">
                                <a
                                    class="btn btn--primary"
                                    href="{{ route('movies.watch', $proposeMovie->slug) }}">
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        aria-hidden="true">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>

                                    Xem ngay
                                </a>

                                @auth
                                    @php
                                        $isFavorited = $proposeMovie->favorites
                                            ->where('user_id', auth()->id())
                                            ->isNotEmpty();
                                    @endphp

                                    <button
                                        type="button"
                                        class="btn btn--ghost"
                                        id="favorite-button"
                                        data-url="{{ route(
                                            'favorites.toggle',
                                            $proposeMovie
                                        ) }}"
                                        onclick="toggleFavorite(this)">
                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <path d="M20 21l-8-4-8 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z" />
                                        </svg>

                                        <span>
                                            {{ $isFavorited
                                                ? 'Đã lưu'
                                                : 'Danh sách của tôi' }}
                                        </span>
                                    </button>

                                    <p id="favorite-message" style="display: none;">
                                </p>
                                @else
                                    <a
                                        class="btn btn--ghost"
                                        href="{{ route('login') }}">
                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M20 21l-8-4-8 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z" />
                                        </svg>

                                        Danh sách của tôi
                                    </a>
                                @endauth

                                @if ($proposeMovie->trailer_url)
                                    <a
                                        class="btn btn--quiet"
                                        href="{{route('movie.detail',$proposeMovie->slug)}}"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        Chi tiết
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

      <!-- ================= XEM TIẾP ================= -->
        <section class="section section--tight" aria-labelledby="xem-tiep">
            <div class="shell">
            <div class="section__head">
                <div>
                <h2 class="section__title" id="xem-tiep">Xem tiếp</h2>
                <p class="section__note">Tiếp tục từ nơi bạn đã dừng lại</p>
                </div>
                <a class="section__link" href="#">Quản lý lịch sử</a>
            </div>

            <ul class="rail">
                @foreach($continueWatching as $watch)
                    @php
                        // 1. Lấy thông tin các model liên quan
                        $movie = $watch->movie;
                        $episode = $watch->episode;
                        $season = $episode?->season; // Lấy thông tin Season từ Episode

                        // 2. Tính toán thời gian còn lại (duration - watch_time)
                        $duration = $episode->duration ?? 0; // Giây
                        $watchTime = $watch->watch_time ?? 0; // Giây
                        $remainingSeconds = max(0, $duration - $watchTime);
                        $remainingMinutes = ceil($remainingSeconds / 60);

                        // 3. Tính % thanh tiến trình xem (Progress Bar)
                        $percent = ($duration > 0) ? min(100, round(($watchTime / $duration) * 100)) : 0;
                    @endphp

                    <li class="resume">
                        <a href="{{ route('movies.watch', ['slug' => $movie->slug, 'ep' => $episode->episode_number]) }}">
                            <div class="resume__thumb">
                                <img
                                    src="{{ asset('Storage/'. $movie->backdrop ?? $movie->poster) }}"
                                    alt="Cảnh phim {{ $movie->title }} - Tập {{ $episode->episode_number }}"
                                    width="640"
                                    height="360"
                                />
                                <span class="resume__play" aria-hidden="true">
                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </span>
                                <span class="resume__left">
                                    @if($remainingMinutes > 0)
                                        Còn {{ $remainingMinutes }} phút
                                    @else
                                        Đã xem xong
                                    @endif
                                </span>
                                <span class="bar"><span style="width: {{ $percent }}%"></span></span>
                            </div>
                            <p class="resume__title">{{ $movie->title }}</p>
                            <p class="resume__sub">
                                {{ $season ? 'Phần ' . $season->season_number . ' · ' : '' }}Tập {{ $episode->episode_number }}
                            </p>
                        </a>
                    </li>
                @endforeach
            </ul>
            </div>
        </section>

        <!-- ================= TOP 10 ================= -->
        <section class="section top10" aria-labelledby="top-10">
            <div class="shell">
                <div class="section__head">
                    <div>
                        <h2 class="section__title" id="top-10">
                            Top 10 thịnh hành tuần này
                        </h2>
                        <p class="section__note">Cập nhật 06:00 hằng ngày</p>
                    </div>
                    <!-- <a class="section__link" href="#">Xem bảng xếp hạng</a> -->
                </div>

                <ol class="rail">
                    @forelse($trendingMovies as $index => $movie)
                        <li class="rank">
                            <span class="rank__num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div class="rank__card">
                                <a href="{{ route('movie.detail', ['slug' => $movie->slug]) }}" style="text-decoration: none; color: inherit;">
                                    <div class="poster">
                                        <img
                                            src="{{ asset('Storage/'. $movie->poster) }}"
                                            alt="Áp phích phim {{ $movie->title }}"
                                            width="336"
                                            height="504"
                                        />
                                        @if($index === 0)
                                            <span class="poster__badge">Top 1</span>
                                        @endif
                                    </div>
                                    <p class="card__title">{{ $movie->title }}</p>
                                    <p class="card__sub">
                                        {{ $movie->genres->first()->name ?? 'Phim' }} 
                                        · 
                                        {{ $movie->release_year ?? $movie->created_at->format('Y') }}
                                    </p>
                                </a>
                            </div>
                        </li>
                    @empty
                        <p style="color: #888;">Chưa có dữ liệu phim thịnh hành trong tuần này.</p>
                    @endforelse
                </ol>
            </div>
        </section>

      <!-- ================= MỚI CẬP NHẬT ================= -->
        <section class="section" id="moi-cap-nhat" aria-labelledby="moi-title">
            <div class="shell">
            <div class="section__head">
                <div>
                <h2 class="section__title" id="moi-title">Mới cập nhật</h2>
                <p class="section__note">
                    Phụ đề Việt và thuyết minh trong vòng 24 giờ
                </p>
                </div>
                <a class="section__link" href="#">Tất cả phim</a>
            </div>

            <ul class="grid">
                @foreach($newMovies as $movie)
                <li class="new-releases-card">
                    <a href="{{ route('movie.detail', ['slug' => $movie->slug]) }}" class="new-releases-card__link">
                        <div class="new-releases-card__poster">
                            <img
                                src="{{ asset('Storage/'.$movie->poster) }}"
                                alt="Áp phích phim {{ $movie->title }}"
                                width="336"
                                height="504"
                                loading="lazy"
                            />
                            
                            <span class="new-releases-card__badge">
                                @if($movie->episodes_count > 0)
                                    {{ $movie->seasons_count > 0 ? $movie->seasons_count . ' Mùa' : 'Tập ' . $movie->episodes_count }}
                                @else
                                    Phim lẻ
                                @endif
                            </span>
                        </div>

                        <p class="new-releases-card__title">{{ $movie->title }}</p>
                        <p class="new-releases-card__sub">
                            {{ $movie->release_year ?? $movie->created_at->format('Y') }}
                            ·
                            {{ $movie->genres->first()->name ?? 'Phim' }}
                        </p>
                    </a>
                </li>
                @endforeach
            </ul>
            </div>
        </section>

        <section class="section" id="phim-danh-cho-ban"
         aria-labelledby="recommend-title">

            <div class="shell">

                <div class="section__head">

                    <div>
                        <h2 class="section__title" id="recommend-title">
                            Phim dành cho bạn
                        </h2>

                        <p class="section__note">
                            Dựa trên sở thích và lịch sử xem của bạn
                        </p>
                    </div>

                    @auth
                        @if($hasRecommendationData)
                            <a class="section__link" href="#">
                                Xem tất cả
                            </a>
                        @else
                            <button
                                type="button"
                                class="section__link recommend-open-btn"
                                onclick="openRecommendationModal()"
                            >
                                Chọn phim đúng gu
                            </button>
                        @endif
                    @endauth

                </div>


                {{-- USER ĐÃ CÓ DỮ LIỆU --}}
                @auth

                    @if($hasRecommendationData && $recommendedMovies->isNotEmpty())

                        <ul class="grid">

                            @foreach($recommendedMovies as $movie)

                                <li class="new-releases-card">

                                    <a
                                        href="{{ route('movie.detail', ['slug' => $movie->slug]) }}"
                                        class="new-releases-card__link"
                                    >

                                        <div class="new-releases-card__poster">

                                            <img
                                                src="{{ $movie->poster
                                                    ? asset('storage/' . $movie->poster)
                                                    : asset('images/no-poster.jpg') }}"
                                                alt="Áp phích phim {{ $movie->title }}"
                                                width="336"
                                                height="504"
                                                loading="lazy"
                                            >

                                            <span class="new-releases-card__badge">

                                                @if($movie->episodes_count > 0)

                                                    @if($movie->seasons_count > 0)
                                                        {{ $movie->seasons_count }} Mùa
                                                    @else
                                                        Tập {{ $movie->episodes_count }}
                                                    @endif

                                                @else
                                                    Phim lẻ
                                                @endif

                                            </span>

                                        </div>

                                        <p class="new-releases-card__title">
                                            {{ $movie->title }}
                                        </p>

                                        <p class="new-releases-card__sub">
                                            {{ $movie->release_year ?? $movie->created_at->format('Y') }}
                                            ·
                                            {{ $movie->genres->first()->name ?? 'Phim' }}
                                        </p>

                                    </a>

                                </li>

                            @endforeach

                        </ul>


                    {{-- USER CHƯA CÓ DỮ LIỆU --}}
                    @else

                        <div class="recommend-empty">

                            <div class="recommend-empty__icon">
                                ✨
                            </div>

                            <h3 class="recommend-empty__title">
                                Chưa biết gu phim của bạn
                            </h3>

                            <p class="recommend-empty__text">
                                Hãy chọn một vài thể loại yêu thích,
                                Mini Cine sẽ tìm những bộ phim phù hợp với bạn.
                            </p>

                            <button
                                type="button"
                                class="recommend-empty__button"
                                onclick="openRecommendationModal()"
                            >
                                ✨ Chọn phim đúng gu
                            </button>

                        </div>

                    @endif

                @else

                    {{-- CHƯA ĐĂNG NHẬP --}}
                    <div class="recommend-empty">

                        <div class="recommend-empty__icon">
                            🎬
                        </div>

                        <h3 class="recommend-empty__title">
                            Khám phá phim dành riêng cho bạn
                        </h3>

                        <p class="recommend-empty__text">
                            Đăng nhập để Mini Cine có thể ghi nhớ sở thích
                            và đề xuất phim phù hợp với bạn.
                        </p>

                        <a
                            href="{{ route('login') }}"
                            class="recommend-empty__button"
                        >
                            Đăng nhập
                        </a>

                    </div>

                @endauth

            </div>

            <div id="recommendModal" class="recommend-modal">

                <div class="recommend-modal__overlay"
                    onclick="closeRecommendationModal()">
                </div>

                <div class="recommend-modal__content">

                    <button
                        type="button"
                        class="recommend-modal__close"
                        onclick="closeRecommendationModal()"
                    >
                        ×
                    </button>


                    <div class="recommend-modal__header">

                        <span class="recommend-modal__step">
                            Bước <span id="recommendStepNumber">1</span>/3
                        </span>

                        <h2>
                            Hãy cho Mini Cine biết gu của bạn
                        </h2>

                        <p>
                            Chọn những gì bạn thích, chúng tôi sẽ tìm phim phù hợp.
                        </p>

                    </div>


                    <form id="recommendForm">

                        {{-- STEP 1 --}}

                        <div
                            class="recommend-step"
                            data-step="1"
                        >

                            <h3>
                                Bạn thích thể loại phim nào?
                            </h3>

                            <p class="recommend-question-note">
                                Có thể chọn nhiều thể loại
                            </p>

                            <div class="recommend-options">

                                @foreach($genres as $genre)

                                    <label class="recommend-option">

                                        <input
                                            type="checkbox"
                                            name="genres[]"
                                            value="{{ $genre->id }}"
                                        >

                                        <span>
                                            {{ $genre->name }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        </div>


                        {{-- STEP 2 --}}

                        <div
                            class="recommend-step"
                            data-step="2"
                            style="display:none;"
                        >

                            <h3>
                                Bạn thường xem phim của quốc gia nào?
                            </h3>

                            <p class="recommend-question-note">
                                Có thể chọn nhiều quốc gia
                            </p>

                            <div class="recommend-options">

                                @foreach($countries as $country)

                                    <label class="recommend-option">

                                        <input
                                            type="checkbox"
                                            name="countries[]"
                                            value="{{ $country->id }}"
                                        >

                                        <span>
                                            {{ $country->name }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        </div>


                        {{-- STEP 3 --}}

                        <div
                            class="recommend-step"
                            data-step="3"
                            style="display:none;"
                        >

                            <h3>
                                Bạn có diễn viên hoặc đạo diễn yêu thích?
                            </h3>

                            <p class="recommend-question-note">
                                Không bắt buộc — có thể bỏ qua.
                            </p>

                            <div class="recommend-options recommend-people">

                                @foreach($people as $person)

                                    <label class="recommend-person">

                                        <input
                                            type="checkbox"
                                            name="people[]"
                                            value="{{ $person->id }}"
                                        >

                                        <div class="recommend-person__avatar">

                                            @if($person->avatar)
                                                <img
                                                    src="{{ asset('storage/' . $person->avatar) }}"
                                                    alt="{{ $person->name }}"
                                                >
                                            @else
                                                <span>
                                                    {{ strtoupper(substr($person->name, 0, 1)) }}
                                                </span>
                                            @endif

                                        </div>

                                        <span>
                                            {{ $person->name }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        </div>


                        <div class="recommend-modal__footer">

                            <button
                                type="button"
                                id="recommendBack"
                                onclick="recommendPreviousStep()"
                                style="display:none;"
                            >
                                Quay lại
                            </button>

                            <button
                                type="button"
                                id="recommendNext"
                                onclick="recommendNextStep()"
                            >
                                Tiếp tục
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </section>
      <!-- ================= THỂ LOẠI ================= -->
        <section class="section" id="the-loai" aria-labelledby="the-loai-title">
            <div class="shell">
            <div class="section__head">
                <div>
                <h2 class="section__title" id="the-loai-title">
                    Duyệt theo thể loại
                </h2>
                </div>
            </div>

            <div class="genres">
            @foreach($filmGenres as $genre)
                <a class="genre" href="{{ route('genres.show', $genre->slug) }}">
                <span>
                    <span class="genre__name">{{ $genre->name }}</span><br />
                    <span class="genre__count">{{ number_format($genre->movies_count, 0, ',', '.') }} phim</span>
                </span>
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    aria-hidden="true"
                >
                    <path d="m9 6 6 6-6 6" />
                </svg>
                </a>
            @endforeach
            </div>
            </div>
        </section>

       
</main>
@endsection


