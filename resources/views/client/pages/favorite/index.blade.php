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
                    aria-label="Lọc theo loại phim">
                    <p class="section__title">
                        Danh sách yêu thích    
                    </p>
                </div>

                @if($favorites->isNotEmpty())
                    <ul class="grid">
                        @foreach($favorites as $favorite)
                            @php
                                $movie = $favorite->movie;
                            @endphp

                            @if($movie)
                                <li
                                    class="card"
                                    data-movie-id="{{ $movie->id }}"
                                >
                                    <a
                                        href="{{ route(
                                            'movie.detail',
                                            $movie->slug
                                        ) }}"
                                    >
                                        <div class="poster">
                                            <img
                                                src="{{ asset('Storage/'. $movie->poster) }}"
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

                                            @if($movie->language === 'vietsub')
                                                · Phụ đề Việt
                                            @elseif($movie->language === 'thuyet_minh')
                                                · Thuyết minh
                                            @elseif($movie->language === 'long_tieng')
                                                · Lồng tiếng
                                            @endif
                                        </p>
                                    </a>

                                    <button
                                        type="button"
                                        class="btn btn--ghost"
                                        data-url="{{ route(
                                            'favorites.toggle',
                                            $movie
                                        ) }}"
                                        onclick="removeFavorite(this)"
                                    >
                                        Đã lưu
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <div class="empty">
                        <p>
                            Bạn chưa lưu bộ phim nào.
                        </p>

                        <a
                            class="btn btn--primary"
                            href="{{ route('home') }}"
                        >
                            Khám phá phim
                        </a>
                    </div>
                @endif

                @if($favorites->hasPages())
                    <div class="pagination">
                        {{ $favorites->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>

    @auth
        <script>
            async function removeFavorite(button) {
                const url = button.dataset.url;

                button.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(
                            data.message ||
                            'Không thể cập nhật danh sách.'
                        );
                    }

                    const card = button.closest('.card');

                    if (card) {
                        card.remove();
                    }

                    const remainingCards = document.querySelectorAll(
                        '.grid .card'
                    );

                    if (remainingCards.length === 0) {
                        window.location.reload();
                    }
                } catch (error) {
                    alert(error.message);

                    button.disabled = false;
                }
            }
        </script>
    @endauth
@endsection