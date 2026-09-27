    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#08090a" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteSeo = \App\Models\SeoSetting::getSettings();
    @endphp

    <!-- Title duy nhất, linh hoạt theo trang -->
    <title>@yield('title', $siteSeo->site_title ?: $systemSettings->site_name)</title>
    
    <meta name="description" content="@yield('description', $siteSeo->site_description ?: 'MINI CINE: nền tảng xem phim trực tuyến với phim lẻ, phim bộ, phim chiếu rạp chất lượng 4K HDR, phụ đề Việt và thuyết minh.')" />
    <meta name="robots" content="@yield('robots', $siteSeo->robots_meta ?: 'index, follow')" />
    <link rel="canonical" href="@yield('canonical', $siteSeo->canonical_url ?: url()->current())" />
    @if(!empty($siteSeo->keywords))
        <meta name="keywords" content="@yield('keywords', $siteSeo->keywords)" />
    @endif
    @if(!empty($siteSeo->google_verification))
        <meta name="google-site-verification" content="{{ $siteSeo->google_verification }}" />
    @endif
    @if(!empty($siteSeo->bing_verification))
        <meta name="msvalidate.01" content="{{ $siteSeo->bing_verification }}" />
    @endif
    @if(!empty($siteSeo->favicon) && file_exists(public_path($siteSeo->favicon)))
        <link rel="icon" href="{{ asset($siteSeo->favicon) }}" />
    @endif

    <!-- Open Graph Meta -->
    <meta property="og:title" content="@yield('og_title', $siteSeo->site_title)" />
    <meta property="og:description" content="@yield('og_description', $siteSeo->site_description)" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    @if(!empty($siteSeo->og_image) && file_exists(public_path($siteSeo->og_image)))
        <meta property="og:image" content="{{ asset($siteSeo->og_image) }}" />
    @endif    <!-- Preconnect tối ưu tốc độ tải Font & CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- CSS Hệ thống -->
    <link rel="stylesheet" href="{{ asset('css/client/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/client/auth.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/client/watch.css') }}" />
     <link rel="stylesheet" href="{{ asset('css/client/forum.css') }}" />
    <!-- Hls.js CDN (Thêm defer để không chặn render giao diện) -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest" defer></script>

    @stack('styles')
