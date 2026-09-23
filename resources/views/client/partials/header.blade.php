<header class="topbar">
  <div class="shell topbar__inner">
  
    <a class="brand" href="/" aria-label="HẮC ẢNH — về trang chủ">
      <img src="{{Storage::url('logos/full_logo.png')}}" alt="HẮC ẢNH">
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
      <form class="search" role="search" action="#">
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
        <input id="q" type="search" placeholder="Tìm phim, diễn viên…" />
      </form>

      <button class="icon-btn" type="button" aria-label="Thông báo mới">
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
      </button>
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