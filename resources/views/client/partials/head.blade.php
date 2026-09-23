    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#08090a" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title duy nhất, linh hoạt theo trang -->
    <title>@yield('title', 'MINI CINE — Xem phim trực tuyến chất lượng 4K')</title>
    
    <meta name="description" content="@yield('description', 'MINI CINE: nền tảng xem phim trực tuyến với phim lẻ, phim bộ, phim chiếu rạp chất lượng 4K HDR, phụ đề Việt và thuyết minh.')" />    <!-- Preconnect tối ưu tốc độ tải Font & CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- CSS Hệ thống -->
    <link rel="stylesheet" href="{{ asset('css/client/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/client/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/client/watch.css') }}" />

    <!-- Hls.js CDN (Thêm defer để không chặn render giao diện) -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest" defer></script>

    @stack('styles')