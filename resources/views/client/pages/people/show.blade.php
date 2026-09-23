@extends('client.layouts.master')

@section('title', $person->name . ' - MINI CINE')

@section('content')

<main>

    <section class="section" id="phim-dien-vien" aria-labelledby="dien-vien-title">

        <div class="shell">

           {{-- Thông tin diễn viên --}}
            <div class="person-header">

                <div class="person-header__avatar">
                    <img
                        src="{{ $person->avatar
                            ? asset('storage/' . $person->avatar)
                            : '/images/cast-1.png' }}"
                        alt="{{ $person->name }}"
                        width="120"
                        height="120"
                    >
                </div>

                <div class="person-header__info">
                    <h1 id="dien-vien-title">
                        {{ $person->name }}
                    </h1>

                    @if($person->biography)
                        <p class="person-bio">
                            {{ $person->biography }}
                        </p>
                    @endif

                    <div class="person-meta">
                        <span class="movie-count">
                            <i class="fas fa-film"></i> {{ $person->movies->count() }} phim tham gia
                        </span>
                        
                        {{-- Nút xem chi tiết thông tin diễn viên --}}
                        <a href="#" class="btn-detail-person">
                            Xem thông tin chi tiết <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Bộ lọc --}}
            <div
                class="chips"
                role="group"
                aria-label="Lọc theo loại phim">
                <button
                    class="chip active"
                    type="button"
                    aria-pressed="true"
                >
                    Tất cả
                </button>

                <button
                    class="chip"
                    type="button"
                    aria-pressed="false"
                >
                    Phim lẻ
                </button>

                <button
                    class="chip"
                    type="button"
                    aria-pressed="false"
                >
                    Phim bộ
                </button>
            </div>


            {{-- Danh sách phim --}}
            <ul class="grid">

                @forelse($person->movies as $movie)

                    <li class="card">

                        <a href="{{ route('movie.detail', $movie->slug) }}">

                            <div class="poster">

                                <img
                                    src="{{ $movie->poster
                                        ? asset('storage/' . $movie->poster)
                                        : '/images/hero-backdrop.png' }}"
                                    alt="Áp phích phim {{ $movie->title }}"
                                    width="336"
                                    height="504"
                                >

                                {{-- Badge --}}
                                @if($movie->type === 'series')

                                    <span class="poster__badge">
                                        Phim bộ
                                    </span>

                                @elseif($movie->quality)

                                    <span class="poster__badge">
                                        {{ $movie->quality }}
                                    </span>

                                @endif

                            </div>

                            <p class="card__title">
                                {{ $movie->title }}
                            </p>

                            <p class="card__sub">

                                {{ $movie->release_year ?? '—' }}

                                ·

                                @switch($movie->language)

                                    @case('vietsub')
                                        Phụ đề Việt
                                        @break

                                    @case('thuyet_minh')
                                        Thuyết minh
                                        @break

                                    @case('long_tieng')
                                        Lồng tiếng
                                        @break

                                    @default
                                        {{ $movie->language }}
                                        
                                @endswitch

                            </p>

                        </a>

                    </li>

                @empty

                    <li>
                        <p>
                            {{ $person->name }} chưa có phim nào.
                        </p>
                    </li>

                @endforelse

            </ul>

        </div>

    </section>

</main>

@endsection