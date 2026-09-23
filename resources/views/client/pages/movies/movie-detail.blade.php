@extends('client.layouts.master')
@section('content')
    <main>
      <!-- ================= HERO CHI TIẾT ================= -->
      <section class="detail-hero" aria-labelledby="movie-title">
        <div class="detail-hero__media">
          <img
            src="{{ asset('storage/' . $movie->backdrop) }}"
            alt=""
            width="1920"
            height="820"
          />
        </div>

        <div class="detail-hero__body">
          <div class="shell">
            <nav class="crumbs" aria-label="Đường dẫn">
              <a href="/index.html">Trang chủ</a>
              <span aria-hidden="true">/</span>
              <a href="/index.html#moi-cap-nhat">Phim lẻ</a>
              <span aria-hidden="true">/</span>
              <a href="/index.html#the-loai">Trinh thám</a>
              <span aria-hidden="true">/</span>
              <span aria-current="page">Vọng Đêm</span>
            </nav>

            <div class="detail-hero__layout">
              <div class="detail-poster">
                <img
                  src="{{ asset('storage/' . $movie->poster) }}"
                  alt="{{$movie->title}}"
                  width="464"
                  height="696"
                />
              </div>

              <div class="detail-info">
                <p class="eyebrow">{{$movie->type}}</p>
                <h1 id="movie-title">{{$movie->title}}</h1>
                <p class="detail-info__alt">
                  {{$movie->original_title}}
                </p>

                <div class="scorebar">
                  <div
                    class="ring"
                    role="img"
                    aria-label="Điểm đánh giá {{ $movie->imdb_rating }} trên 10"
                    style="background: conic-gradient(var(--accent) 0 {{ $movie->imdb_rating * 10 }}%, rgba(255, 255, 255, 0.12) {{ $movie->imdb_rating * 10 }}% 100%);"
                  >
                    <span class="ring__val">{{ $movie->imdb_rating }}<small>/10</small></span>
                  </div>

                  <div class="scorebar__facts">
                    <span class="score">{{ $movie->imdb_rating ?? $movie->imdb_rating }} IMDb</span>
                    <span class="dot" aria-hidden="true"></span>
                    <span>{{ $movie->release_year ?? \Carbon\Carbon::parse($movie->release_date)->year }}</span>
                    <span class="dot" aria-hidden="true"></span>
                    <span>
                        @if($movie->type === 'single')
                            {{ $movie->duration ?? 0 }} phút
                        @else
                            {{ $movie->episodes->count() }} tập
                        @endif
                    </span>
                    <span class="dot" aria-hidden="true"></span>
                    <span>{{ $movie->genres->pluck('name')->implode(' · ') }}</span>
                    <span class="tag">{{ $movie->quality ?? '4K HDR' }}</span>
                    <!-- <span class="tag">T{{ $movie->age_limit ?? '18' }}</span> -->
                    <span class="tag">{{$movie->language}}</span>
                  </div>
                </div>
                <p class="detail-info__desc">
                  {{$movie->short_description}}
                </p>

                <div class="detail-cta">
                  <a class="btn btn--primary" href="{{ route('movies.watch', $movie->slug) }}">
                    <svg
                      width="15"
                      height="15"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                      aria-hidden="true"
                    >
                      <path d="M8 5v14l11-7z" />
                    </svg>
                    Xem ngay
                  </a>

                  <a class="btn btn--ghost" href="#trailer">
                    <svg
                      width="15"
                      height="15"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      aria-hidden="true"
                    >
                      <path d="M15 10l5.5-3v10L15 14" />
                      <rect x="2.5" y="6" width="12.5" height="12" rx="2" />
                    </svg>
                    Xem trailer
                  </a>

                    @auth
                    @php
                        $isFavorited = $movie->favorites
                            ->where('user_id', auth()->id())
                            ->isNotEmpty();
                    @endphp

                    <button
                        type="button"
                        class="btn btn--ghost"
                        id="favorite-button"
                        data-url="{{ route(
                            'favorites.toggle',
                            $movie
                        ) }}"
                        onclick="toggleFavorite(this)"
                    >
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
                @else
                    <a
                        class="btn btn--ghost"
                        href="{{ route('login') }}"
                    >
                        <svg
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            aria-hidden="true"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>

                        Danh sách của tôi
                    </a>
                @endauth

                  <a class="btn btn--quiet" href="#danh-gia">Đánh giá phim</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ================= NỘI DUNG ================= -->
      <div class="shell layout">
        <!-- ----- Cột chính ----- -->
        <div>
          <section class="block" aria-labelledby="danh-sach-tap">
            <h2 class="block__title" id="danh-sach-tap">Danh sách tập</h2>
            <div class="episodes-list" style="display: flex; gap: 10px; flex-wrap: wrap;">
              @foreach($movie->episodes as $episode)
                  <a
                      class="btn btn--ghost btn--sm btn--episode"
                      href="{{ route('movies.watch', [
                          'slug' => $movie->slug,
                          'ep' => $episode->episode_number
                      ]) }}"
                  >
                      Tập {{ $episode->episode_number }}
                  </a>
              @endforeach
            </div>
          </section>
          <!-- Nội dung phim -->
          <section class="block" aria-labelledby="noi-dung">
            <h2 class="block__title" id="noi-dung">Nội dung phim</h2>
            <div class="prose">
              <p>
                {{$movie->description}}
              </p>
            </div>
          </section>

          <!-- Trailer & clip -->
          <section class="block" aria-labelledby="trailer-title">
            <h2 class="block__title" id="trailer-title">
                Trailer &amp; clip
            </h2>

            <div class="clips">

                @if($movie->trailer_url)

                    <a
                        class="clip"
                        href="#trailer"
                        data-trailer-url="{{ $movie->trailer_url }}"
                    >
                        <div class="clip__thumb">

                            <img
                                src="{{ asset('Storage/'. $movie->thumbnail) ?? '/images/hero-backdrop.png' }}"
                                alt="{{ $movie->title }}"
                                width="640"
                                height="360"
                            >

                            <span class="clip__play" aria-hidden="true">
                                <span>
                                    <svg width="18" height="18"
                                        viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </span>
                            </span>

                            <span class="clip__dur">
                                Trailer
                            </span>

                        </div>

                        <p class="clip__label">
                            Trailer {{ $movie->title }}
                        </p>

                        <p class="clip__sub">
                            TRAILER
                        </p>
                    </a>

                @else

                    <p>Phim chưa có trailer.</p>

                @endif
            </div>
            
          </section>

          <!-- Diễn viên -->
          <section class="block" aria-labelledby="dien-vien">

              <h2 class="block__title" id="dien-vien">
                  Diễn viên & Đạo diễn
              </h2>

              <ul class="cast">

                  @forelse($movie->people as $person)

                    <li class="cast__item">

                        <a
                            href="{{ route('people.show', $person->slug) }}"
                            class="cast__link"
                        >

                            <img
                                src="{{ $person->avatar
                                    ? asset('storage/' . $person->avatar)
                                    : '/images/cast-1.png' }}"
                                alt="Diễn viên {{ $person->name }}"
                                width="104"
                                height="104"
                            >

                            <span>

                                <span class="cast__name">
                                    {{ $person->name }}
                                </span>

                                <br>

                                <span class="cast__role">
                                    Diễn viên
                                </span>

                            </span>

                        </a>

                    </li>

                @empty

                      <li>
                          Chưa có thông tin diễn viên.
                      </li>

                  @endforelse

              </ul>

          </section>

          <!-- Đánh giá -->
          <section
              class="block"
              id="danh-gia"
              aria-labelledby="danh-gia-title">
              <h2 class="block__title" id="danh-gia-title">
                  Đánh giá của người xem
              </h2>

              <div class="rating">
                  <div class="rating__big">
                      <p class="rating__num">
                          <span id="rating-average">
                              {{ number_format($ratingAverage, 1) }}
                          </span>
                          <sub>/10</sub>
                      </p>

                      <div
                          class="stars-static"
                          role="img"
                          aria-label="{{ $ratingAverage }}/10"
                          id="rating-stars"
                      >
                          @for($i = 1; $i <= 5; $i++)
                              <svg
                                  class="{{ $ratingAverage >= ($i * 2) ? '' : 'off' }}"
                                  width="15"
                                  height="15"
                                  viewBox="0 0 24 24"
                                  fill="currentColor"
                                  aria-hidden="true"
                              >
                                  <path d="M12 2l2.9 6.3 6.9.8-5 4.7 1.3 6.8L12 17.4 5.9 20.6 7.2 13.8l-5-4.7 6.9-.8z" />
                              </svg>
                          @endfor
                      </div>

                      <p class="rating__count">
                          <span id="rating-count">
                              {{ number_format($ratingCount) }}
                          </span>
                          lượt đánh giá
                      </p>
                  </div>

                  <div class="rating__bars" id="rating-bars">
                      @for($star = 5; $star >= 1; $star--)
                          <div class="rating__row">
                              <b>
                                  {{ $star }} sao
                              </b>

                              <span class="track">
                                  <span
                                      style="width: {{ $ratingPercentages[$star] }}%"
                                  ></span>
                              </span>

                              <span class="rating__pct">
                                  {{ $ratingPercentages[$star] }}%
                              </span>
                          </div>
                      @endfor
                  </div>
              </div>

              @auth
                  <form
                        class="yourrate"
                        id="rating-form"
                        action="{{ route('ratings.store') }}"
                        method="POST"
                        onsubmit="submitRating(event)">
                      @csrf

                      <input
                          type="hidden"
                          name="movie_id"
                          value="{{ $movie->id }}"
                      >

                      <fieldset class="stars">
                          <legend class="yourrate__label">
                              Bạn cho phim này mấy sao?
                          </legend>

                          @for($star = 5; $star >= 1; $star--)
                              <input
                                  type="radio"
                                  id="star{{ $star }}"
                                  name="rating"
                                  value="{{ $star }}"
                              >

                              <label for="star{{ $star }}">
                                  <span class="sr-only">
                                      {{ $star }} sao
                                  </span>

                                  <svg
                                      width="30"
                                      height="30"
                                      viewBox="0 0 24 24"
                                      fill="currentColor"
                                      aria-hidden="true"
                                  >
                                      <path d="M12 2l2.9 6.3 6.9 0.8-5 4.7 1.3 6.8L12 17.4 5.9 20.6 7.2 13.8l-5-4.7 6.9-.8z" />
                                  </svg>
                              </label>
                          @endfor
                      </fieldset>

                      <button
                          class="btn btn--primary"
                          type="submit"
                          id="rating-submit"
                      >
                          Gửi đánh giá
                      </button>

                      <p
                          class="yourrate__hint"
                          id="rating-message"
                          style="display: none;"
                      ></p>
                  </form>
              @else
                  <p class="yourrate__hint">
                      Vui lòng đăng nhập để đánh giá phim.
                  </p>
              @endauth
          </section>

          <!-- bình luận -->
          <section class="block" id="binh-luan" aria-labelledby="binh-luan-title">

              <div class="section__head">
                  <div>
                      <h2 class="block__title" id="binh-luan-title" style="margin: 0">
                          Bình luận
                      </h2>

                      <p class="section__note">
                          <span id="comments-count">
                              {{ number_format($movie->comments->count()) }}
                          </span>
                          bình luận · Sắp xếp: Mới nhất
                      </p>
                  </div>

                  <a class="section__link" href="#">
                      Nội quy bình luận
                  </a>
              </div>

              @auth
                  <form
                      class="commentform"
                      action="{{ route('comments.store') }}"
                      method="POST"
                      id="comment-form"
                  >
                      @csrf

                      <input
                          type="hidden"
                          name="movie_id"
                          value="{{ $movie->id }}"
                      >

                      <input
                          type="hidden"
                          name="parent_id"
                          id="parent-id"
                          value=""
                      >

                      <img
                          src="{{ auth()->user()->avatar
                              ? asset('storage/' . auth()->user()->avatar)
                              : asset('images/avatar-default.png') }}"
                          alt="Ảnh đại diện của bạn"
                          width="40"
                          height="40"
                      >

                      <div class="commentform__body">
                          <label class="sr-only" for="cmt">
                              Nội dung bình luận
                          </label>

                          <textarea
                              id="cmt"
                              name="content"
                              placeholder="Chia sẻ cảm nhận của bạn về phim…"
                              maxlength="2000"
                              required
                          >{{ old('content') }}</textarea>

                          <div class="commentform__foot">
                              <span
                                  class="commentform__note"
                                  id="reply-status"
                              >
                                  Hãy bình luận văn minh, tránh tiết lộ tình tiết quan trọng.
                              </span>

                              <div>
                                  <button
                                      class="btn btn--ghost btn--sm"
                                      type="button"
                                      id="cancel-reply"
                                      style="display: none;"
                                  >
                                      Hủy trả lời
                                  </button>

                                  <button
                                      class="btn btn--primary btn--sm"
                                      type="submit"
                                      id="comment-submit"
                                  >
                                      Gửi bình luận
                                  </button>
                              </div>
                          </div>
                      </div>
                  </form>
              @else
                  <p class="yourrate__hint">
                      Vui lòng đăng nhập để bình luận.
                  </p>
              @endauth

              <ul class="comments" id="comments-list">
                  @forelse($movie->comments as $comment)
                      @php
                          $commentLiked = auth()->check()
                              ? $comment->likes->contains('user_id', auth()->id())
                              : false;
                      @endphp

                      <li
                          class="comment"
                          data-comment-id="{{ $comment->id }}"
                      >
                          <img
                              src="{{asset('storage/'.$comment->user->avatar)}}"
                              alt="Ảnh đại diện"
                              width="40"
                              height="40"
                          >

                          <div class="comment__body">
                              <div class="comment__head">
                                  <span class="comment__name">
                                      {{ $comment->user->game_name ?? $comment->user->name }}
                                  </span>

                                  <span class="comment__time">
                                      {{ $comment->created_at->diffForHumans() }}
                                  </span>
                              </div>

                              <p class="comment__text">
                                  {{ $comment->content }}
                              </p>

                              <div class="comment__actions">
                                  @auth
                                      <button
                                          type="button"
                                          class="comment-like-button {{ $commentLiked ? 'liked' : '' }}"
                                          data-comment-id="{{ $comment->id }}"
                                      >
                                          <span class="like-icon">
                                              {{ $commentLiked ? '❤️' : '👍' }}
                                          </span>

                                          <span class="like-count">
                                              {{ $comment->likes->count() }}
                                          </span>
                                      </button>

                                      <button
                                          type="button"
                                          class="reply-button"
                                          data-comment-id="{{ $comment->id }}"
                                          data-comment-name="{{ $comment->user->game_name ?? $comment->user->name }}"
                                      >
                                          Trả lời
                                      </button>
                                  @else
                                      <button type="button">
                                          👍 {{ $comment->likes->count() }}
                                      </button>

                                      <button type="button">
                                          Trả lời
                                      </button>
                                  @endauth

                                  <button type="button">
                                      Báo cáo
                                  </button>
                              </div>

                              @foreach($comment->replies as $reply)
                                  @php
                                      $replyLiked = auth()->check()
                                          ? $reply->likes->contains('user_id', auth()->id())
                                          : false;
                                  @endphp

                                  <div
                                      class="comment comment--reply"
                                      data-comment-id="{{ $reply->id }}"
                                  >
                                      <img
                                          src="/placeholder-user.jpg"
                                          alt="Ảnh đại diện"
                                          width="40"
                                          height="40"
                                      >

                                      <div class="comment__body">
                                          <div class="comment__head">
                                              <span class="comment__name">
                                                  {{ $reply->user->game_name ?? $reply->user->name }}
                                              </span>

                                              <span class="comment__time">
                                                  {{ $reply->created_at->diffForHumans() }}
                                              </span>
                                          </div>

                                          <p class="comment__text">
                                              {{ $reply->content }}
                                          </p>

                                          <div class="comment__actions">
                                              @auth
                                                  <button
                                                      type="button"
                                                      class="comment-like-button {{ $replyLiked ? 'liked' : '' }}"
                                                      data-comment-id="{{ $reply->id }}"
                                                  >
                                                      <span class="like-icon">
                                                          {{ $replyLiked ? '❤️' : '👍' }}
                                                      </span>

                                                      <span class="like-count">
                                                          {{ $reply->likes->count() }}
                                                      </span>
                                                  </button>
                                              @else
                                                  <button type="button">
                                                      👍 {{ $reply->likes->count() }}
                                                  </button>
                                              @endauth

                                              <button type="button">
                                                  Báo cáo
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              @endforeach
                          </div>
                      </li>
                  @empty
                      <li id="no-comments">
                          <p>
                              Chưa có bình luận nào.
                              Hãy là người đầu tiên bình luận!
                          </p>
                      </li>
                  @endforelse
              </ul>

              <div class="comments__more">
                  <a class="btn btn--ghost btn--sm" href="#">
                      Xem thêm bình luận
                  </a>
              </div>
          </section>
        </div>

        <!-- ----- Sidebar ----- -->
        <aside class="side" aria-label="Thông tin phụ">
          <section
              class="panel"
              aria-labelledby="thong-tin">
              <h2 id="thong-tin">
                  Thông tin phim
              </h2>

              <dl class="facts">
                  <div>
                      <dt>Đạo diễn</dt>
                      <dd>
                          @php
                              $directors = $movie->people->where(
                                  'pivot.role',
                                  'director'
                              );
                          @endphp

                          @forelse($directors as $director)
                              <a href="{{ route('people.show', $director->slug) }}">
                                  {{ $director->name }}
                              </a>{{ !$loop->last ? ', ' : '' }}
                          @empty
                              —
                          @endforelse
                      </dd>
                  </div>

                  <div>
                      <dt>Quốc gia</dt>
                      <dd>
                          @forelse($movie->countries as $country)
                              <span>
                                  {{ $country->name }}
                              </span>{{ !$loop->last ? ', ' : '' }}
                          @empty
                              —
                          @endforelse
                      </dd>
                  </div>

                  <div>
                      <dt>Khởi chiếu</dt>
                      <dd>
                          {{ $movie->release_date
                              ? $movie->release_date->format('d/m/Y')
                              : ($movie->release_year ?? '—') }}
                      </dd>
                  </div>

                  <div>
                      <dt>Thời lượng</dt>
                      <dd>
                          {{ $movie->duration
                              ? $movie->duration . ' phút'
                              : '—' }}
                      </dd>
                  </div>

                  <div>
                      <dt>Thể loại</dt>
                      <dd>
                          @forelse($movie->genres as $genre)
                              <a href="#">
                                  {{ $genre->name }}
                              </a>{{ !$loop->last ? ', ' : '' }}
                          @empty
                              —
                          @endforelse
                      </dd>
                  </div>

                  <div>
                      <dt>Chất lượng</dt>
                      <dd>
                          {{ $movie->quality ?? '—' }}
                      </dd>
                  </div>

                  <div>
                      <dt>Phụ đề</dt>
                      <dd>
                          @php
                              $subtitleLanguages = $movie->episodes
                                  ->flatMap->subtitles
                                  ->where('is_active', true)
                                  ->pluck('label')
                                  ->unique()
                                  ->values();
                          @endphp

                          @forelse($subtitleLanguages as $language)
                              {{ $language }}{{ !$loop->last ? ', ' : '' }}
                          @empty
                              —
                          @endforelse
                      </dd>
                  </div>
              </dl>
          </section>

          <section
              class="panel"
              aria-labelledby="tuong-tu">
              <h2 id="tuong-tu">
                  Phim tương tự
              </h2>

              <ul class="simlist">
                  @forelse($similarMovies as $similarMovie)
                      <li>
                          <a
                              class="sim"
                              href="{{ route('movie.detail', $similarMovie->slug) }}"
                          >
                              <img
                                  src="{{ $similarMovie->poster
                                      ? asset('storage/' . $similarMovie->poster)
                                      : asset('images/poster-default.png') }}"
                                  alt="Áp phích phim {{ $similarMovie->title }}"
                                  width="108"
                                  height="156"
                              >

                              <span>
                                  <span class="sim__title">
                                      {{ $similarMovie->title }}
                                  </span>

                                  <span class="sim__sub">
                                      {{ $similarMovie->release_year ?? '—' }}
                                      ·
                                      <span class="sim__score">
                                          {{ number_format($similarMovie->rating, 1) }}
                                      </span>
                                  </span>
                              </span>
                          </a>
                      </li>
                  @empty
                      <li>
                          <span>
                              Chưa có phim tương tự.
                          </span>
                      </li>
                  @endforelse
              </ul>
          </section>
        </aside>
      </div>
    </main>

    <!-- ================= CỬA SỔ TRAILER ================= -->
    <div
        class="modal"
        id="trailer"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title">

        <a
            class="modal__backdrop"
            href="#"
            aria-label="Đóng cửa sổ trailer"
        ></a>

        <div class="modal__panel">

            <div class="modal__head">

                <h2 class="modal__title" id="modal-title">

                    Trailer chính thức

                    <small>
                        {{ $movie->title }}
                    </small>

                </h2>

                <a
                    class="modal__close"
                    href="#"
                    aria-label="Đóng"
                >
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
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </a>

            </div>

            <div class="player">

                <iframe
                    id="trailer-iframe"
                    width="100%"
                    height="100%"
                    src=""
                    title="Trailer {{ $movie->title }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>

            </div>

        </div>
    </div>

@endsection
