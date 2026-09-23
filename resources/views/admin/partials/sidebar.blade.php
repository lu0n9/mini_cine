<aside class="sidebar">
    <div class="brand">
      <div class="logo">C</div>
      <div>
        <h1>CineAdmin</h1>
        <span>Streaming</span>
      </div>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="nav-item solo">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
      Bảng điều khiển
    </a>

    <details class="group" open>
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20M7 4v4M17 4v4"/></svg>
        Phim
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.movies.index') }}" class="nav-item">Tất cả phim <span class="badge">{{$totalMovie}}</span></a>
      <a href="{{ route('admin.movies.create') }}" class="nav-item">Thêm phim</a>
      <a href="{{ route('admin.movies.featured') }}" class="nav-item">Phim nổi bật</a>
      <a href="{{ route('admin.movies.popular') }}" class="nav-item">Phim phổ biến</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 7h5M17 17h5"/></svg>
        Tập phim
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.episodes') }}" class="nav-item">Episodes</a>
      <a href="{{ route('admin.seasons') }}" class="nav-item">Seasons</a>
      <a href="{{ route('admin.servers') }}" class="nav-item">Video Servers</a>
      <a href="{{ route('admin.subtitles') }}" class="nav-item">Subtitles</a>
      <a href="{{ route('admin.videos.upload') }}" class="nav-item">Upload</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M4 9h16M9 4v16"/></svg>
        Nội dung
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.content.genres') }}" class="nav-item">Thể loại</a>
      <a href="{{ route('admin.countries') }}" class="nav-item">Quốc gia</a>
      <a href="{{ route('admin.people') }}" class="nav-item">Diễn viên, đạo diễn</a>
      <a href="{{ route('admin.content.tags') }}" class="nav-item">Tags</a>
      <a href="{{ route('admin.collections') }}" class="nav-item">Collections</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6"/></svg>
        Người dùng
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.users') }}" class="nav-item">Users <span class="badge">{{$userCount}}</span></a>
      <a href="{{ route('admin.admins') }}" class="nav-item">Admins <span class="badge">{{$adminCount}}</span></a>
      <a href="{{ route('admin.roles.index') }}" class="nav-item">Roles</a>
      <a href="{{ route('admin.permissions') }}" class="nav-item">Permissions</a>
      <a href="{{ route('admin.matrix.index') }}" class="nav-item">Matrix</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Tương tác
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.comments.index') }}" class="nav-item">Comments <span class="badge warn">{{$newCommentCount}}</span></a>
      <a href="{{ route('admin.ratings.index') }}" class="nav-item">Ratings</a>
      <a href="{{ route('admin.reports.index') }}" class="nav-item">Reports <span class="badge warn">{{$newReportCount}}</span></a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m7 14 3-4 3 3 4-6"/></svg>
        Thống kê
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.views.index') }}" class="nav-item">Movie Views</a>
      <a href="{{ route('admin.history.index') }}" class="nav-item">Watch History</a>
      <a href="{{ route('admin.statistical.popular_stats') }}" class="nav-item">Popular Movies</a>
      <a href="{{ route('admin.statistical.user_stats') }}" class="nav-item">User Statistics</a>
      <a href="{{ route('admin.statistical.traffic') }}" class="nav-item">Traffic</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        Giao diện
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.interface.homepage') }}" class="nav-item">Homepage</a>
      <a href="{{ route('admin.interface.banners') }}" class="nav-item">Banners</a>
      <a href="{{ route('admin.menus.index') }}" class="nav-item">Menus</a>
      <a href="{{ route('admin.interface.pages') }}" class="nav-item">Pages</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        Thông báo
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.notifications') }}" class="nav-item">Notifications</a>
      <a href="{{ route('admin.email') }}" class="nav-item">Email</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4-4"/></svg>
        SEO
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.seo') }}" class="nav-item">SEO Settings</a>
      <a href="{{ route('admin.sitemap') }}" class="nav-item">Sitemap</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5z"/></svg>
        Premium
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.premium.plans') }}" class="nav-item">Plans</a>
      <a href="{{ route('admin.premium.subscriptions') }}" class="nav-item">Subscriptions</a>
      <a href="{{ route('admin.premium.transactions') }}" class="nav-item">Transactions</a>
      <a href="{{ route('admin.premium.coupons') }}" class="nav-item">Coupons</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5M3 12c0 1.7 4 3 9 3s9-1.3 9-3"/></svg>
        Hệ thống
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="{{ route('admin.system.storage') }}" class="nav-item">Storage</a>
      <a href="{{ route('admin.system.cache') }}" class="nav-item">Cache</a>
      <a href="{{ route('admin.system.backup') }}" class="nav-item">Backup</a>
      <a href="{{ route('admin.system.activity-log') }}" class="nav-item">Activity Logs</a>
      <a href="{{ route('admin.system.cron') }}" class="nav-item">Cron Jobs</a>
      <a href="{{ route('admin.system.api') }}" class="nav-item">API</a>
    </details>

    <a href="{{ route('admin.setting.index') }}" class="nav-item solo">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 7 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7H1a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 2.6 7"/></svg>
      Cài đặt
    </a>

    <div class="sidebar-foot" id="userMenuToggle">
      <img src="/posters/silent-echo.png" alt="Ảnh đại diện quản trị viên" />
      <div class="who">Luong Tran<small>Super Admin</small></div>
      
      <!-- Thêm menu dropdown chứa nút đăng xuất -->
      <div class="dropdown-menu" id="logoutDropdown">
        <form action="{{Route('admin.logout')}}" method="post">
          @csrf
          <button class="logout-btn">Đăng xuất</button>
        </form>
      </div>
    </div>
  </aside>