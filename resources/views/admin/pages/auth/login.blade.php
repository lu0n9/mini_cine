 @extends('admin.layouts.auth')
 @section('content')
 <div class="bg" aria-hidden="true">
    <div class="bg-grid"></div>
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
    <div class="beams">
      <span class="beam"></span><span class="beam"></span><span class="beam"></span>
      <span class="beam"></span><span class="beam"></span><span class="beam"></span>
    </div>
    <div class="particles" id="particles"></div>
  </div>

  <main class="card">
    <div class="brand">
      <div class="brand-mark">C</div>
      <div class="brand-name">CineAdmin</div>
    </div>
    <p class="subtitle">Bảng điều khiển quản trị web xem phim</p>

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf

      <div class="field">
          <label for="email">Email quản trị</label>
          <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="4" width="20" height="16" rx="2"/>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>

              <input
                  id="email"
                  name="email"
                  type="email"
                  placeholder="admin@cineadmin.vn"
                  value="{{ old('email') }}"
                  required
                  autocomplete="username"
              />
          </div>

          @error('email')
              <small>{{ $message }}</small>
          @enderror
      </div>

      <div class="field">
          <label for="password">Mật khẩu</label>
          <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>

              <input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="••••••••"
                  required
                  autocomplete="current-password"
              />

              <button type="button" class="toggle-pass" id="togglePass" aria-label="Hiện mật khẩu">
                  <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                      <circle cx="12" cy="12" r="3"/>
                  </svg>
              </button>
          </div>

          @error('password')
              <small>{{ $message }}</small>
          @enderror
      </div>

      <div class="row">
          <label class="checkbox">
              <input type="checkbox" name="remember" />
              Ghi nhớ đăng nhập
          </label>

          <a class="link" href="#">Quên mật khẩu?</a>
      </div>

      <button class="btn" type="submit">Đăng nhập</button>

      <div class="divider">Bảo mật hai lớp</div>
    </form>

    <p class="footer-note">© 2026 CineAdmin. Chỉ dành cho quản trị viên được cấp quyền.</p>
  </main>

  <script>
    // Password visibility toggle
    const toggle = document.getElementById('togglePass');
    const pass = document.getElementById('password');
    const eye = document.getElementById('eyeIcon');
    toggle.addEventListener('click', () => {
      const show = pass.type === 'password';
      pass.type = show ? 'text' : 'password';
      toggle.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
      eye.innerHTML = show
        ? '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>'
        : '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
    });

    // Generate floating particles
    const container = document.getElementById('particles');
    const count = 26;
    for (let i = 0; i < count; i++) {
      const dot = document.createElement('span');
      dot.className = 'dot';
      dot.style.left = Math.random() * 100 + '%';
      const dur = 8 + Math.random() * 12;
      dot.style.animationDuration = dur + 's';
      dot.style.animationDelay = -(Math.random() * dur) + 's';
      const size = 2 + Math.random() * 3;
      dot.style.width = size + 'px';
      dot.style.height = size + 'px';
      dot.style.opacity = (0.3 + Math.random() * 0.5).toFixed(2);
      container.appendChild(dot);
    }
  </script>
@endsection