@extends('admin.layouts.master')

@section('content')
<section id="settings" class="page">
    <div class="page-head">
        <div>
            <h3>Cài đặt</h3>
            <p>Cấu hình thương hiệu và các hành vi đang được website sử dụng.</p>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="notify-alert success" style="margin-bottom: 20px; padding: 12px 16px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 8px; color: #10b981; font-size: 13.5px;">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="notify-alert error" style="margin-bottom: 20px; padding: 12px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; color: #ef4444; font-size: 13.5px;">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="notify-alert error" style="margin-bottom: 20px; padding: 12px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; color: #ef4444; font-size: 13.5px;">
            <strong>Vui lòng kiểm tra lại các trường thông tin:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="split">
        <nav class="subnav">
            <a href="#general-settings" class="on">General</a>
            <a href="#player-settings">Player</a>
            <a href="#registration-settings">Đăng ký</a>
            <a href="{{ route('admin.seo') }}">SEO</a>
            <a href="{{ route('admin.email') }}">Email &amp; SMTP</a>
            <a href="{{ route('admin.comments.index') }}">Kiểm duyệt bình luận</a>
        </nav>

        <div>
            <form action="{{ route('admin.setting.update') }}" method="POST" id="systemSettingsForm">
                @csrf

                {{-- PANEL 1: GENERAL SETTINGS --}}
                <div class="panel" id="general-settings">
                    <div class="panel-head">
                        <h4>General Settings</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-grid">
                            <div class="field">
                                <label for="site_name">Website name</label>
                                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings->site_name) }}" placeholder="VD: CineAdmin hoặc MINI CINE" required>
                            </div>
                            <div class="field">
                                <label for="contact_email">Email liên hệ</label>
                                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" placeholder="contact@cineadmin.vn">
                            </div>
                            <div class="field">
                                <label for="contact_hotline">Hotline</label>
                                <input type="text" name="contact_hotline" id="contact_hotline" value="{{ old('contact_hotline', $settings->contact_hotline) }}" placeholder="1900 0000">
                            </div>
                            <div class="field">
                                <label for="timezone">Timezone</label>
                                <select name="timezone" id="timezone">
                                    <option value="Asia/Ho_Chi_Minh" {{ old('timezone', $settings->timezone) === 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>GMT+7 (Việt Nam)</option>
                                    <option value="UTC" {{ old('timezone', $settings->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                                </select>
                            </div>
                        </div>

                        <div class="setting-row" style="margin-top: 8px;">
                            <div class="st-txt">
                                <strong>Maintenance mode</strong>
                                <small>Tạm khóa website để bảo trì</small>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}>
                                <span class="track"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- PANEL 2: PLAYER SETTINGS --}}
                <div class="panel" id="player-settings">
                    <div class="panel-head">
                        <h4>Player Settings</h4>
                    </div>
                    <div class="panel-body">
                        <div class="setting-row">
                            <div class="st-txt">
                                <strong>Autoplay</strong>
                                <small>Tự phát khi mở trang xem</small>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="player_autoplay" value="1" {{ old('player_autoplay', $settings->player_autoplay) ? 'checked' : '' }}>
                                <span class="track"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <div class="st-txt">
                                <strong>Auto next episode</strong>
                                <small>Tự chuyển tập kế tiếp</small>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="player_auto_next" value="1" {{ old('player_auto_next', $settings->player_auto_next) ? 'checked' : '' }}>
                                <span class="track"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <div class="st-txt">
                                <strong>Picture in Picture</strong>
                                <small>Cho phép thu nhỏ trình phát</small>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="player_pip" value="1" {{ old('player_pip', $settings->player_pip) ? 'checked' : '' }}>
                                <span class="track"></span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- PANEL 3: REGISTRATION & SECURITY --}}
                <div class="panel" id="registration-settings">
                    <div class="panel-head">
                        <h4>Đăng ký</h4>
                    </div>
                    <div class="panel-body">
                        <div class="setting-row">
                            <div class="st-txt">
                                <strong>Cho phép đăng ký</strong>
                                <small>Người dùng mới có thể tạo tài khoản</small>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" name="allow_registration" value="1" {{ old('allow_registration', $settings->allow_registration) ? 'checked' : '' }}>
                                <span class="track"></span>
                            </label>
                        </div>

                        <div class="form-actions" style="margin-top: 14px;">
                            <button type="reset" class="btn ghost">Hủy</button>
                            <button type="submit" class="btn">Lưu cài đặt</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Xử lý chuyển tab / scroll tới panel khi click subnav
        const subnavLinks = document.querySelectorAll('.subnav a[href^="#"]');
        subnavLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    subnavLinks.forEach(l => l.classList.remove('on'));
                    this.classList.add('on');
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Tự động highlight subnav tương ứng khi cuộn trang
        const panels = document.querySelectorAll('.split .panel[id]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    subnavLinks.forEach(link => {
                        if (link.getAttribute('href') === '#' + id) {
                            subnavLinks.forEach(l => l.classList.remove('on'));
                            link.classList.add('on');
                        }
                    });
                }
            });
        }, {
            rootMargin: '-20% 0px -70% 0px',
            threshold: 0
        });

        panels.forEach(panel => observer.observe(panel));
    });
</script>
@endpush
@endsection
