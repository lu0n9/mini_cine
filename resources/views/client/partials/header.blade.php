<header class="topbar">
  <div class="shell topbar__inner">
  
    <a class="brand" href="/" aria-label="{{ $systemSettings->site_name }} — về trang chủ">
    @php($brandParts = preg_split('/\s+/', trim($systemSettings->site_name), 2))
    <span class="brand-logo">
        <span class="brand-logo-main">{{ $brandParts[0] ?? $systemSettings->site_name }}</span>
        @if (!empty($brandParts[1]))<span class="brand-logo-sub">{{ $brandParts[1] }}</span>@endif
    </span>
</a>

    <!-- DESKTOP / PC -->
    <div class="nav-wrapper">
        <nav class="nav" aria-label="Điều hướng chính">

            @foreach($mainMenus as $menu)

                @if($menu->children->count())

                    <div class="nav-dropdown">

                        <button
                            type="button"
                            class="nav-dropdown-toggle"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            {{ $menu->name }}
                            <span class="nav-arrow"></span>
                        </button>

                        <div class="nav-dropdown-menu multi-column">

                            @foreach($menu->children as $child)
                                <a href="{{ $child->url ?: '#' }}">
                                    {{ $child->name }}
                                </a>
                            @endforeach

                        </div>

                    </div>

                @else

                    <a href="{{ $menu->url ?: '#' }}">
                        {{ $menu->name }}
                    </a>

                @endif

            @endforeach

        </nav>
    </div>

    <div class="topbar__actions">
      <form class="search" id="header-search-form" role="search" action="{{ route('movies.index') }}" method="GET" autocomplete="off">
        <label class="sr-only" for="q">Tìm phim, diễn viên, thể loại</label>
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
          <circle cx="11" cy="11" r="7" />
          <path d="m20 20-3.5-3.5" />
        </svg>
        <input id="q" name="search" type="search" placeholder="Tìm phim, diễn viên, thể loại…" aria-autocomplete="list" aria-controls="header-search-suggestions" aria-expanded="false" />
        <button class="sr-only" type="submit">Tìm kiếm</button>
        <div id="header-search-suggestions" class="header-search-suggestions" role="listbox" aria-label="Gợi ý tìm kiếm" hidden></div>
      </form>

      <a href="{{ route('premium.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 12px;border-radius:999px;background:#6d28d9;color:#fff;text-decoration:none;font-weight:700;white-space:nowrap">✦ Premium</a>

      <div class="notification-menu" id="headerNotificationMenu">
        <button
          class="icon-btn notification-toggle"
          id="headerNotificationToggle"
          type="button"
          aria-label="Thông báo{{ auth('web')->check() && $unreadNotificationCount ? ', ' . $unreadNotificationCount . ' chưa đọc' : '' }}"
          aria-haspopup="true"
          aria-expanded="false"
          aria-controls="headerNotificationDropdown"
        >
          <svg
            width="17"
            height="17"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            aria-hidden="true">
            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
          </svg>
          @if (auth('web')->check() && $unreadNotificationCount > 0)
            <span class="notification-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
          @endif
        </button>

        <div class="notification-dropdown" id="headerNotificationDropdown" aria-label="Thông báo mới nhất">
          <div class="notification-dropdown__heading">
            <strong>Thông báo</strong>
            @if (auth('web')->check() && $unreadNotificationCount > 0)
              <span>{{ $unreadNotificationCount }} chưa đọc</span>
            @endif
          </div>

          @auth('web')
            @forelse ($headerNotifications as $notification)
              <a
                class="notification-preview {{ $notification->read_at ? '' : 'is-unread' }}"
                href="{{ route('notifications.show', $notification->id) }}"
              >
                <span class="notification-preview__dot" aria-hidden="true"></span>
                <span class="notification-preview__content">
                  <strong>{{ $notification->title }}</strong>
                  <span>{{ \Illuminate\Support\Str::limit($notification->message, 90) }}</span>
                  <time datetime="{{ $notification->created_at->toIso8601String() }}">
                    {{ $notification->created_at->diffForHumans() }}
                  </time>
                </span>
              </a>
            @empty
              <p class="notification-dropdown__empty">Bạn chưa có thông báo nào.</p>
            @endforelse

            <a class="notification-dropdown__all" href="{{ route('notifications.index') }}">Xem tất cả thông báo</a>
          @else
            <p class="notification-dropdown__empty">Đăng nhập để xem thông báo của bạn.</p>
            <a class="notification-dropdown__all" href="{{ route('login') }}">Đăng nhập</a>
          @endauth
        </div>
      </div>
      @if (auth()->check())
          <div class="user-menu-container">

              <button
                  type="button"
                  class="user-link"
                  id="userMenuToggle"
                  aria-label="Mở menu tài khoản"
                  aria-expanded="false"
              >
                @if(!empty(auth()->user()->avatar))
                    <img
                        class="avatar"
                        src="{{ asset('Storage/' . auth()->user()->avatar) }}"
                        alt="Ảnh đại diện của bạn"
                        width="38"
                        height="38"/>
                @else
                    <img
                        class="avatar"
                        src="{{ asset('Storage/avatars/avatar_default.jpg') }}"
                        alt="Ảnh đại diện của bạn"
                        width="38"
                        height="38"/>
                @endif
              </button>

              <div class="dropdown-menu" id="userDropdown" role="menu">

                  <a
                      href="{{ route('profile') }}"
                      class="dropdown-item"
                      role="menuitem"
                  >
                      Profile
                  </a>

                  <a href="{{ route('premium.index') }}" class="dropdown-item" role="menuitem">✦ Gói Premium</a>

                  <a
                      href="{{ route('favorites.index') }}"
                      class="dropdown-item"
                      role="menuitem"
                  >
                      Favourite
                  </a>

                  <a
                      href="{{ route('history.index') }}"
                      class="dropdown-item"
                      role="menuitem"
                  >
                      History
                  </a>
                    <a 
                        href="{{ route('forum.my-posts') }}"
                        class="dropdown-item"
                        role="menuitem">
                        Bài viết của tôi
                    </a>

                  <div class="dropdown-divider"></div>

                  <form action="{{ route('logout') }}" method="POST">
                      @csrf

                      <button
                          type="submit"
                          class="dropdown-item logout-btn"
                          role="menuitem"
                      >
                          Logout
                      </button>
                  </form>

              </div>
          </div>

      @else

          <a href="{{ route('login') }}" aria-label="Đăng nhập">
              <img
                  class="avatar"
                  src="{{ asset('Storage/avatars/avatar_default.jpg') }}"
                  alt="Ảnh đại diện của bạn"
                  width="38"
                  height="38"
              />
          </a>

      @endif
    </div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('header-search-form');
    const input = document.getElementById('q');
    const list = document.getElementById('header-search-suggestions');
    if (!form || !input || !list) return;

    const endpoint = @json(route('search.suggestions'));
    let timer;
    let activeRequest;

    function closeSuggestions() {
        list.hidden = true;
        list.replaceChildren();
        input.setAttribute('aria-expanded', 'false');
    }

    function renderSuggestions(results) {
        list.replaceChildren();
        if (!results.length) {
            const empty = document.createElement('div');
            empty.className = 'header-search-empty';
            empty.textContent = 'Không tìm thấy gợi ý phù hợp';
            list.appendChild(empty);
        } else {
            results.forEach(function (result) {
                const link = document.createElement('a');
                link.className = 'header-search-result';
                link.href = result.url;
                link.setAttribute('role', 'option');

                const image = document.createElement('img');
                image.src = result.image;
                image.alt = '';
                image.loading = 'lazy';

                const details = document.createElement('span');
                details.className = 'header-search-result-details';
                const title = document.createElement('strong');
                title.textContent = result.title;
                const subtitle = document.createElement('small');
                subtitle.textContent = result.subtitle || result.kind;
                details.append(title, subtitle);

                const kind = document.createElement('span');
                kind.className = 'header-search-kind';
                kind.textContent = result.kind;
                link.append(image, details, kind);
                list.appendChild(link);
            });

            const allResults = document.createElement('button');
            allResults.type = 'submit';
            allResults.className = 'header-search-all';
            allResults.textContent = 'Xem tất cả kết quả';
            list.appendChild(allResults);
        }

        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    }

    input.addEventListener('input', function () {
        window.clearTimeout(timer);
        if (activeRequest) activeRequest.abort();
        const term = input.value.trim();
        if (term.length < 2) {
            closeSuggestions();
            return;
        }

        timer = window.setTimeout(async function () {
            activeRequest = new AbortController();
            try {
                const response = await fetch(endpoint + '?q=' + encodeURIComponent(term), {
                    headers: { 'Accept': 'application/json' },
                    signal: activeRequest.signal
                });
                if (!response.ok) throw new Error('Search request failed');
                const data = await response.json();
                if (input.value.trim() === term) renderSuggestions(data.results || []);
            } catch (error) {
                if (error.name !== 'AbortError') closeSuggestions();
            }
        }, 220);
    });

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeSuggestions();
        if (event.key === 'ArrowDown' && !list.hidden) {
            const firstResult = list.querySelector('a');
            if (firstResult) {
                event.preventDefault();
                firstResult.focus();
            }
        }
    });

    document.addEventListener('click', function (event) {
        if (!form.contains(event.target)) closeSuggestions();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('headerNotificationMenu');
    const toggle = document.getElementById('headerNotificationToggle');
    if (!menu || !toggle) return;

    function closeMenu() {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        const isOpen = menu.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function (event) {
        if (!menu.contains(event.target)) closeMenu();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeMenu();
            toggle.focus();
        }
    });
});
</script>
