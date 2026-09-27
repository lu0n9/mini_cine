@extends('client.layouts.master')
@section('title', 'Đang xem ' . $movie->title . ' - ' . ($currentEpisode->name ?? 'Tập ' . $currentEpisode->episode_number) . ' | MINI CINE')
@section('description', 'Xem phim ' . $movie->title . ' ' . ($currentEpisode->name ?? 'Tập ' . $currentEpisode->episode_number) . ' chất lượng 4K HDR, phụ đề Việt chuẩn tại MINI CINE.')
@push('styles')
<!-- CSS Tùy chỉnh để đảm bảo khung video không bị bể giao diện -->
<style>
    .video-container {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background-color: #000;
        border-radius: 8px;
        overflow: hidden;
    }
    .video-js-player {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@section('content')        
<div class="movie-page-container">
@if(session('report_success'))
    <div class="report-success-message">
        ✓ {{ session('report_success') }}
    </div>
@endif
    @if(!$hasPremiumAccess)
        <div id="premium-watch-notice" style="padding:14px 18px;margin:0 0 16px;border:1px solid #7c3aed;background:#1e1b4b;border-radius:10px;color:#e9d5ff">
            <strong>Gói miễn phí:</strong> bạn xem được tối đa 10% thời lượng phim này.
            <a href="{{ route('premium.index') }}" style="color:white;font-weight:700;margin-left:8px">Nâng cấp Premium để xem toàn bộ →</a>
        </div>
        <div id="premium-watch-locked" style="display:none;padding:24px;margin:0 0 16px;text-align:center;border:1px solid #7c3aed;background:#1e1b4b;border-radius:10px;color:#fff">
            <strong>Đã hết thời lượng xem miễn phí của phim này.</strong>
            <a href="{{ route('premium.index') }}" style="display:inline-block;margin-left:10px;color:#ddd6fe;font-weight:700">Chọn gói Premium</a>
        </div>
    @endif
    <!-- 1. KHUNG PHÁT VIDEO CHÍNH -->
    <div class="video-player-wrapper">
        <div class="video-container">
            <video 
                id="main-video-player" 
                class="video-js-player" 
                controls 
                crossorigin="anonymous"
                @if($systemSettings->player_autoplay) autoplay @endif
                @if(!$systemSettings->player_pip) disablepictureinpicture @endif
                preload="metadata" 
                poster="{{ $movie->backdrop ?? 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1280&q=80' }}">
                
                @foreach($subtitles as $subtitle)
                    <track
                        kind="subtitles"
                        src="{{ $subtitle->file_url }}"
                        srclang="{{ $subtitle->language }}"
                        label="{{ $subtitle->label }}"
                        {{ $subtitle->is_default ? 'default' : '' }}
                    >
                @endforeach

                Trình duyệt của bạn không hỗ trợ phát Video.
            </video>
        </div>
    </div>

    <!-- 2. THANH TIỆN ÍCH TOP -->
    <div class="top-utility-bar">
        <div class="utility-left">
            <button class="util-btn">♡ Yêu thích</button>
            <button class="util-btn">+ Thêm vào</button>
            <button class="util-btn toggle-btn">
                ⏩ Bỏ qua giới thiệu <span class="badge-status">ON</span>
            </button>
            <button class="util-btn toggle-btn dark">
                📺 Rạp phim <span class="badge-status off">OFF</span>
            </button>
        </div>
        <div class="utility-right">
            <button class="util-btn">↪ Chia sẻ</button>
            <button
                type="button"
                class="util-btn"
                onclick="openReportModal()"
            >
                🚩 Báo lỗi
            </button>
        </div>
    </div>

    <!-- 3. BỐ CỤC CHÍNH (LƯỚI 2 CỘT) -->
    <div class="main-movie-grid">

        <!-- CỘT TRÁI: THÔNG TIN PHIM & BÌNH LUẬN -->
        <div class="left-column">
            
            <div class="movie-header-card">
                <img src="{{ $movie->poster ?? 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80' }}" alt="{{ $movie->title }}" class="poster-img">
                <div class="movie-details">
                    <h1 class="movie-title">{{ $movie->title }}</h1>
                    <div class="movie-badges">
                        <span class="badge-quality">{{ $currentEpisode->quality ?? $movie->quality }}</span>
                        <span class="badge-episodes">Tập {{ $currentEpisode->episode_number }}/{{ $movie->episodes->count() }}</span>
                    </div>
                    <p class="movie-desc">
                        {{ $currentEpisode->description ?? $movie->description ?? 'Chưa có mô tả cho bộ phim này.' }}
                    </p>
                    <a href="#" class="btn-more-info">Xem thông tin phim ›</a>
                </div>
            </div>

            <div class="comments-section">
                <div class="comments-header">
                    <h3>Bình luận</h3>
                    <div class="filter-dropdown">
                        <button class="dropdown-btn">Mới nhất ∨</button>
                    </div>
                </div>

                <p class="comment-notice">
                    Nếu không phiền, bạn hãy để lại bình luận chia sẻ cảm nhận nhé — trang web sẽ sôi động hơn nhiều! 😊
                </p>

                @guest
                    <div class="login-prompt-card">
                        <div class="prompt-info">
                            <div class="icon-box">➔</div>
                            <div>
                                <h4>Đăng nhập để tham gia bình luận</h4>
                                <p>Chia sẻ cảm nhận về bộ phim cùng cộng đồng</p>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="btn-login" style="text-decoration: none; display: inline-block;">Đăng nhập</a>
                    </div>
                @endguest

                <div class="empty-comments">
                    Chưa có bình luận nào. Hãy là người đầu tiên!
                </div>
            </div>

        </div>

        <!-- CỘT PHẢI: SERVER, TẬP PHIM & DIỄN VIÊN -->
        <div class="right-column">
            
            <div class="sidebar-block">
                <div class="block-title">
                    <span class="icon">≡</span> Chọn Server
                </div>
                <div class="server-tags">
                    @forelse($currentEpisode->sources as $source)
                        <span class="server-badge" style="margin-right: 5px;">
                            {{ $source->name }} <small>{{ strtoupper($source->type) }}</small>
                        </span>
                    @empty
                        <span class="server-badge">Premium <small>AUTO</small></span>
                    @endforelse
                </div>
            </div>

            <div class="sidebar-block">
                <div class="block-header">
                    <div class="block-title">
                        <span class="icon">►</span> Danh sách tập
                    </div>
                    <button class="sub-btn">CC {{ $movie->language }}</button>
                </div>
                <div class="episode-grid">
                    @foreach($movie->episodes as $ep)
                        <a href="{{ route('movies.watch', ['slug' => $movie->slug, 'episode' => $ep->id]) }}"
                           class="ep-btn {{ $ep->id === $currentEpisode->id ? 'active' : '' }}"
                           style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                            {{ $ep->episode_number }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="sidebar-block">
                <div class="block-title">
                    <span class="icon">👑</span> Diễn viên
                </div>
                <div class="cast-grid">
                    <div class="cast-item">
                        <img src="https://ui-avatars.com/api/?name=Daniel+Ibanez&background=22222b&color=fff" alt="Cast">
                        <span class="cast-name">Daniel Ibáñez</span>
                    </div>
                    <div class="cast-item">
                        <img src="https://ui-avatars.com/api/?name=Itziar+Manero&background=22222b&color=fff" alt="Cast">
                        <span class="cast-name">Itziar Manero</span>
                    </div>
                    <div class="cast-item">
                        <img src="https://ui-avatars.com/api/?name=Ricardo+Gomez&background=22222b&color=fff" alt="Cast">
                        <span class="cast-name">Ricardo Gómez</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <button class="btn-scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">^</button>

</div>
<!-- Report Modal -->
<div
    id="reportModal"
    class="report-modal"
    aria-hidden="true"
>
    <div
        class="report-modal-overlay"
        onclick="closeReportModal()"
    ></div>

    <div
        class="report-modal-content"
        role="dialog"
        aria-modal="true"
        aria-labelledby="reportModalTitle"
    >

        <div class="report-modal-header">

            <div>
                <h3 id="reportModalTitle">
                    Báo lỗi phim
                </h3>

                <p>
                    Hãy cho chúng tôi biết vấn đề bạn đang gặp phải.
                </p>
            </div>

            <button
                type="button"
                class="report-close"
                onclick="closeReportModal()"
                aria-label="Đóng"
            >
                ×
            </button>

        </div>

        <form
            action="{{ route('reports.store') }}"
            method="POST"
            id="reportForm">
            @csrf

            <input
                type="hidden"
                name="movie_id"
                value="{{ $movie->id }}"
            >

            @if(isset($episode) && $episode)
                <input
                    type="hidden"
                    name="episode_id"
                    value="{{ $episode->id }}"
                >
            @endif


            {{-- Loại lỗi --}}
            <div class="report-field">

                <label for="reportType">
                    Loại lỗi
                </label>

                <select
                    id="reportType"
                    name="type"
                    required
                >
                    <option value="">
                        -- Chọn loại lỗi --
                    </option>

                    <option value="video_error">
                        Video không phát được
                    </option>

                    <option value="subtitle_error">
                        Sai / mất phụ đề
                    </option>

                    <option value="wrong_information">
                        Thông tin phim không chính xác
                    </option>

                    {{-- 
                        Các option khác phải dùng ĐÚNG giá trị ENUM
                        đang có trong bảng reports.
                    --}}
                </select>

            </div>


            {{-- Nội dung lỗi --}}
            <div class="report-field">

                <label for="reportMessage">
                    Mô tả lỗi
                </label>

                <textarea
                    id="reportMessage"
                    name="message"
                    rows="5"
                    maxlength="2000"
                    placeholder="Ví dụ: Tập 4 không phát được từ phút 12:30..."
                    required
                ></textarea>

                <div class="report-counter">
                    <span id="reportCharCount">0</span>/2000
                </div>

            </div>


            <div class="report-info">
                <span>ⓘ</span>

                <span>
                    Báo lỗi sẽ được gửi đến quản trị viên để kiểm tra.
                </span>
            </div>


            <div class="report-actions">

                <button
                    type="button"
                    class="report-btn report-btn-cancel"
                    onclick="closeReportModal()"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="report-btn report-btn-submit"
                >
                    Gửi báo lỗi
                </button>

            </div>

        </form>

    </div>
</div>
<script>
    function openReportModal() {
        const modal = document.getElementById('reportModal');

        if (!modal) {
            return;
        }

        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('report-modal-open');

        setTimeout(() => {
            document.getElementById('reportType')?.focus();
        }, 100);
    }

    function closeReportModal() {
        const modal = document.getElementById('reportModal');

        if (!modal) {
            return;
        }

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('report-modal-open');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeReportModal();
        }
    });

    const reportContent = document.getElementById('reportContent');
    const reportCharCount = document.getElementById('reportCharCount');

    if (reportContent && reportCharCount) {

        reportContent.addEventListener('input', function () {
            reportCharCount.textContent = this.value.length;
        });

    }
</script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    (function () {
        const video = document.getElementById('main-video-player');
        const streamUrl = @json($streamUrl ?? '');
        const savedWatchTime = @json($savedWatchTime ?? 0);
        const isAuthenticated = @json(auth()->check());
        const hasPremiumAccess = @json($hasPremiumAccess);
        const configuredWatchLimit = @json($watchLimitSeconds);
        const autoNextEpisode = @json($systemSettings->player_auto_next);
        const nextEpisodeUrl = @json($nextEpisode ? route('movies.watch', ['slug' => $movie->slug, 'episode' => $nextEpisode->id]) : null);
        let watchLimit = configuredWatchLimit;
        let hasSeeked = false;

        if (video && streamUrl) {
            video.disablePictureInPicture = !@json($systemSettings->player_pip);
            if (autoNextEpisode && nextEpisodeUrl) {
                video.addEventListener('ended', function () {
                    window.location.assign(nextEpisodeUrl);
                });
            }

            // 1. Khởi tạo HLS
            if (Hls.isSupported()) {
                const hls = new Hls({ capLevelToPlayerSize: true, autoStartLoad: true });
                hls.loadSource(streamUrl);
                hls.attachMedia(video);

                hls.on(Hls.Events.MANIFEST_PARSED, function () {
                    if (savedWatchTime > 0 && !hasSeeked) {
                        video.currentTime = savedWatchTime;
                        hasSeeked = true;
                        console.log('⏩ Tiếp tục xem từ:', savedWatchTime, 'giây');
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = streamUrl;
                video.addEventListener('loadedmetadata', function() {
                    if (savedWatchTime > 0 && !hasSeeked) {
                        video.currentTime = savedWatchTime;
                        hasSeeked = true;
                    }
                });
            }

            if (!hasPremiumAccess) {
                const lockedMessage = document.getElementById('premium-watch-locked');
                const applyWatchLimit = function () {
                    if (watchLimit === null && Number.isFinite(video.duration) && video.duration > 0) {
                        watchLimit = Math.floor(video.duration * 0.10);
                    }
                    if (watchLimit === 0) {
                        video.pause();
                        lockedMessage.style.display = 'block';
                        return;
                    }
                    if (watchLimit !== null && video.currentTime >= watchLimit) {
                        video.currentTime = watchLimit;
                        video.pause();
                        lockedMessage.style.display = 'block';
                    }
                };
                video.addEventListener('timeupdate', applyWatchLimit);
                video.addEventListener('seeking', function () {
                    if (watchLimit !== null && video.currentTime > watchLimit) {
                        video.currentTime = watchLimit;
                    }
                });
                video.addEventListener('loadedmetadata', applyWatchLimit);
            }

            // 2. Tự động lưu tiến trình xem (chỉ thực hiện khi User đã đăng nhập)
            if (isAuthenticated) {
                let lastSaved = 0;
                
                video.addEventListener('timeupdate', function () {
                    const currentTime = Math.floor(video.currentTime);
                    const duration = Math.floor(video.duration) || 0;

                    // Chỉ gửi API lưu mỗi 10 giây một lần
                    if (currentTime > 0 && (currentTime - lastSaved >= 10)) {
                        lastSaved = currentTime;

                        fetch("{{ route('api.save_history') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                movie_id: @json($movie->id),
                                episode_id: @json($currentEpisode->id),
                                watch_time: currentTime,
                                duration: duration
                            })
                        })
                        .then(response => response.json())
                        .then(data => console.log('💾 [Save History]:', data))
                        .catch(error => console.error('💥 [Save History Error]:', error));
                    }
                });
            }
        }
    })();
</script>
@endsection
