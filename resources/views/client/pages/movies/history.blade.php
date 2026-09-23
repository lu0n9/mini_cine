@extends('client.layouts.master')

@section('content')
    <main>
        <section
            class="section"
            id="moi-cap-nhat"
            aria-labelledby="moi-title"
        >
            <div class="shell">
                <div
                    class="chips"
                    role="group"
                    aria-label="Lịch sử xem phim"
                >
                    <p class="section__title">
                        Lịch sử xem phim
                    </p>
                </div>

                @if($history->isNotEmpty())
                    <ul class="grid">
                        @foreach($history as $item)
                            @php
                                $movie = $item->movie;
                                $episode = $item->episode;
                            @endphp

                            @if($movie)
                                <li
                                    class="card"
                                    data-movie-id="{{ $movie->id }}"
                                >
                                    <a
                                        href="{{ route(
                                            'movie.detail',
                                            [
                                                'slug' => $movie->slug,
                                                'ep' => $episode?->episode_number
                                            ]
                                        ) }}"
                                    >
                                        <div class="poster">
                                            <img
                                                src="{{ asset(
                                                    'storage/' . $movie->poster
                                                ) }}"
                                                alt="Áp phích phim {{ $movie->title }}"
                                                width="336"
                                                height="504"
                                            >

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

                                            @if($episode)
                                                · Tập
                                                {{ $episode->episode_number }}
                                            @endif

                                            @if($movie->language === 'vietsub')
                                                · Phụ đề Việt
                                            @elseif($movie->language === 'thuyet_minh')
                                                · Thuyết minh
                                            @elseif($movie->language === 'long_tieng')
                                                · Lồng tiếng
                                            @endif
                                        </p>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <div class="empty">
                        <p>
                            Bạn chưa xem bộ phim nào.
                        </p>

                        <a
                            class="btn btn--primary"
                            href="{{ route('home') }}"
                        >
                            Khám phá phim
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection