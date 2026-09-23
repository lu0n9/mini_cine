<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Lạc Kịch Bản | MiniCine</title>
    <link rel="stylesheet" href="{{ asset('css/errors/404.css') }}">
</head>
<body>
    <div class="cinema-screen">
        <div class="lens-flare"></div>
        <div class="projector-grid" aria-hidden="true"></div>
        
        <header class="site-header">
            <a href="/" class="brand" aria-label="Cinebox - Trang chủ">
                <span class="brand-badge">🎬</span>
                <span class="brand-text">MINI<span>CINE</span></span>
            </a>
            <div class="live-status">
                <span class="pulse-dot"></span>
                <span>Trạng thái: Cắt cảnh (Cut Scene)</span>
            </div>
        </header>

        <main class="error-container">
            <div class="clapperboard">
                <span class="clip-top"></span>
                <div class="error-badge">SCENE 404 • TAKE 01</div>
            </div>

            <div class="glitch-wrapper" data-text="404">
                <h1 class="glitch-number">404</h1>
            </div>

            <div class="script-box">
                <h2>Ôi thôi!</h2>
                <p>Có vẻ như cuộn phim bạn đang tìm kiếm đã thất lạc, đổi tên bản chiếu hoặc chưa từng xuất hiện trong lịch phát sóng của chúng tôi.</p>
            </div>

            <div class="control-deck">
                <a href="{{route('home')}}" class="btn-primary">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Quay Về Sảnh Chính
                </a>
                <a href="{{route('movies.index')}}" class="btn-secondary">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Khám Phá Phim Khác
                </a>
            </div>

            <a href="javascript:history.back()" class="return-link">← Quay lại màn hình trước</a>
        </main>

        <div class="film-roll left" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
        <div class="film-roll right" aria-hidden="true"><span></span><span></span><span></span><span></span></div>

        <footer class="site-footer">
            <p>© 2026 MINICINE ENTERTAINMENT</p>
            <p class="footer-quote">"Mọi thước phim đều có kết thúc, nhưng hành trình tìm kiếm thì không."</p>
        </footer>
    </div>
</body>
</html>