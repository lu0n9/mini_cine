<footer class="footer">
    <div class="shell">
    <div class="footer__grid">
        <div class="footer__about">
        <a class="brand" href="/">
            <span class="brand__mark" aria-hidden="true">{{ mb_strtoupper(mb_substr($systemSettings->site_name, 0, 2)) }}</span>
            <span class="brand__name">{{ $systemSettings->site_name }}</span>
        </a>
        <p>
            Nền tảng xem phim trực tuyến với kho phim lẻ, phim bộ và phim
            chiếu rạp chất lượng 4K HDR, phụ đề Việt cập nhật mỗi ngày.
        </p>
        @if($systemSettings->contact_email)
            <p><a href="mailto:{{ $systemSettings->contact_email }}">{{ $systemSettings->contact_email }}</a></p>
        @endif
        @if($systemSettings->contact_hotline)
            <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $systemSettings->contact_hotline) }}">{{ $systemSettings->contact_hotline }}</a></p>
        @endif
        </div>

        @foreach($footerMenus as $menu)

    <div>

        <h2>{{ $menu->name }}</h2>

        @if($menu->children->count())

            <ul>

                @foreach($menu->children as $child)

                    <li>
                        <a href="{{ $child->url ?: '#' }}">
                            {{ $child->name }}
                        </a>
                    </li>

                @endforeach

            </ul>

        @elseif($menu->url)

            <ul>
                <li>
                    <a href="{{ $menu->url }}">
                        {{ $menu->name }}
                    </a>
                </li>
            </ul>

        @endif

    </div>

@endforeach
    </div>

    <div class="footer__bottom">
        <p>© {{ now()->year }} {{ $systemSettings->site_name }}. Mọi hình ảnh chỉ dùng cho mục đích minh hoạ.</p>
        <p>{{ $systemSettings->timezone }} · Tiếng Việt</p>
    </div>
    </div>
     <nav class="mobile-bottom-nav" aria-label="Điều hướng mobile">

        {{-- Trang chủ --}}
        <a
            href="{{ route('home') }}"
            class="mobile-bottom-item {{ request()->routeIs('home') ? 'active' : '' }}"
        >
            <span class="mobile-bottom-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 10.5L12 3l9 7.5"></path>
                    <path d="M5.5 9.5V21h13V9.5"></path>
                    <path d="M9.5 21v-6h5v6"></path>
                </svg>
            </span>

            <span class="mobile-bottom-label">
                Trang chủ
            </span>
        </a>


        {{-- Tìm kiếm --}}
        <a
            href="{{ route('movies.index') }}"
            class="mobile-bottom-item"
        >
            <span class="mobile-bottom-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="6.5"></circle>
                    <path d="M16 16l5 5"></path>
                </svg>
            </span>

            <span class="mobile-bottom-label">
                Tìm kiếm
            </span>
        </a>


        {{-- Danh mục --}}
        <button
            type="button"
            class="mobile-bottom-item mobile-bottom-category"
            aria-label="Danh mục"
        >
            <span class="mobile-bottom-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                </svg>
            </span>

            <span class="mobile-bottom-label">
                Danh mục
            </span>
        </button>


        {{-- Khám phá --}}
        <a
            href="#"
            class="mobile-bottom-item"
        >
            <span class="mobile-bottom-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z"></path>
                    <path d="M19 16l.8 2.2L22 19l-2.2.8L19 22l-.8-2.2L16 19l2.2-.8L19 16z"></path>
                </svg>
            </span>

            <span class="mobile-bottom-label">
                Khám phá
            </span>
        </a>


        {{-- Hồ sơ --}}
        <a
            href="{{ auth()->check() ? route('profile') : route('login') }}"
            class="mobile-bottom-item"
        >
            <span class="mobile-bottom-avatar">
                {{ strtoupper(substr(auth()->user()->game_name ?? auth()->user()->name ?? 'L', 0, 1)) }}
            </span>

            <span class="mobile-bottom-label">
                Hồ sơ
            </span>
        </a>

    </nav>
</footer>
<!-- {{-- =========================================================
     MOBILE BOTTOM NAVIGATION
     Chỉ dành cho mobile
     ========================================================= --}} -->

   
