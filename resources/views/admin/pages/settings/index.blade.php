@extends('admin.layouts.master')

@section('content')
<section id="settings" class="page">
    <div class="page-head">
        <div>
            <h3>Cài đặt</h3>
            <p>Cấu hình chung, phim, trình phát, đăng ký, bình luận, đăng nhập xã hội và bảo mật.</p>
        </div>
    </div>

    <div class="split">
        <nav class="subnav">
            <a class="on">General</a>
            <a href="#player-settings">Player</a>
            <a href="#registration-settings">Registration & Security</a>
            <!-- <a href="#comments-settings">Comments</a>
            <a href="#email-settings">Email</a> -->
            <a href="#social-login-settings">Social Login</a>
            <!-- <a href="#security-settings">Security</a> -->
        </nav>

        <div>
            <div class="panel" id="general-settings">
                <div class="panel-head">
                    <h4>General Settings</h4>
                </div>
                <div class="panel-body">
                    <form class="form-grid" onsubmit="return false">
                        <div class="field">
                            <label>Website name</label>
                            <input value="CineAdmin">
                        </div>
                        <div class="field">
                            <label>Website URL</label>
                            <input value="https://cineadmin.vn">
                        </div>
                        <div class="field">
                            <label>Email liên hệ</label>
                            <input value="contact@cineadmin.vn">
                        </div>
                        <div class="field">
                            <label>Hotline</label>
                            <input value="1900 0000">
                        </div>
                        <div class="field">
                            <label>Timezone</label>
                            <select>
                                <option>GMT+7 (Việt Nam)</option>
                                <option>UTC</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Ngôn ngữ mặc định</label>
                            <select>
                                <option>Tiếng Việt</option>
                                <option>English</option>
                            </select>
                        </div>
                    </form>

                    <div class="setting-row" style="margin-top: 8px;">
                        <div class="st-txt">
                            <strong>Maintenance mode</strong>
                            <small>Tạm khóa website để bảo trì</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox">
                            <span class="track"></span>
                        </span>
                    </div>
                </div>
            </div>

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
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Auto next episode</strong>
                            <small>Tự chuyển tập kế tiếp</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Skip intro / outro</strong>
                            <small>Nút bỏ qua giới thiệu</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Picture in Picture</strong>
                            <small>Cho phép thu nhỏ trình phát</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox">
                            <span class="track"></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="panel" id="registration-settings">
                <div class="panel-head">
                    <h4>Registration &amp; Security</h4>
                </div>
                <div class="panel-body">
                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Cho phép đăng ký</strong>
                            <small>Người dùng mới có thể tạo tài khoản</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Xác thực email</strong>
                            <small>Bắt buộc verify email khi đăng ký</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Yêu cầu duyệt bình luận</strong>
                            <small>Bình luận cần admin duyệt trước</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox">
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Two-factor authentication</strong>
                            <small>Bảo mật 2 lớp cho quản trị viên</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Giới hạn đăng nhập sai</strong>
                            <small>Khóa tạm sau 5 lần sai</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="form-actions" style="margin-top: 14px;">
                        <button class="btn ghost">Hủy</button>
                        <button class="btn">Lưu cài đặt</button>
                    </div>
                </div>
            </div>

            <div class="panel" id="social-login-settings">
                <div class="panel-head">
                    <h4>Social Login</h4>
                </div>
                <div class="panel-body">
                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Google Login</strong>
                            <small>Client ID · Callback URL</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox" checked>
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>Facebook Login</strong>
                            <small>App ID · App Secret</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox">
                            <span class="track"></span>
                        </span>
                    </div>

                    <div class="setting-row">
                        <div class="st-txt">
                            <strong>GitHub Login</strong>
                            <small>Client ID · Client Secret</small>
                        </div>
                        <span class="toggle">
                            <input type="checkbox">
                            <span class="track"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection