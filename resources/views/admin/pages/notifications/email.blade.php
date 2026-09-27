@extends('admin.layouts.master')

@section('content')
<section id="email-settings" class="page">
    <div class="page-head">
        <div>
            <h3>Cấu hình Email & SMTP</h3>
            <p>Thiết lập thông số máy chủ gửi email SMTP, kiểm tra kết nối và xem trước các mẫu email hệ thống.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.notifications') }}" class="btn ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                Quản lý thông báo
            </a>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="email-alert success">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="email-alert error">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="email-alert error">
            <strong>Vui lòng kiểm tra lại các trường thông tin:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid-2">
        {{-- PANEL 1: CẤU HÌNH SMTP --}}
        <div class="panel">
            <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
                <h4>Cấu hình máy chủ SMTP</h4>
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight:600;">
                    {{ strtoupper($smtpConfig['mail_mailer']) }}
                </span>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.email.settings') }}" method="POST" class="form-grid" id="smtpForm">
                    @csrf

                    <div class="field">
                        <label for="mail_mailer">Mail Driver</label>
                        <select name="mail_mailer" id="mail_mailer">
                            <option value="smtp" {{ old('mail_mailer', $smtpConfig['mail_mailer']) === 'smtp' ? 'selected' : '' }}>SMTP (Khuyên dùng)</option>
                            <option value="sendmail" {{ old('mail_mailer', $smtpConfig['mail_mailer']) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log" {{ old('mail_mailer', $smtpConfig['mail_mailer']) === 'log' ? 'selected' : '' }}>Log (Ghi file log)</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="mail_host">SMTP Host</label>
                        <input type="text" name="mail_host" id="mail_host"
                               value="{{ old('mail_host', $smtpConfig['mail_host']) }}"
                               placeholder="VD: smtp.gmail.com" required>
                    </div>

                    <div class="field">
                        <label for="mail_port">SMTP Port</label>
                        <input type="number" name="mail_port" id="mail_port"
                               value="{{ old('mail_port', $smtpConfig['mail_port']) }}"
                               placeholder="587 hoặc 465" required>
                    </div>

                    <div class="field">
                        <label for="mail_encryption">Mã hóa (Encryption)</label>
                        <select name="mail_encryption" id="mail_encryption">
                            <option value="tls" {{ old('mail_encryption', $smtpConfig['mail_encryption']) === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                            <option value="ssl" {{ old('mail_encryption', $smtpConfig['mail_encryption']) === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                            <option value="none" {{ in_array(old('mail_encryption', $smtpConfig['mail_encryption']), ['none', '']) ? 'selected' : '' }}>Không mã hóa (Port 25)</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="mail_username">Username / Email đăng nhập</label>
                        <input type="text" name="mail_username" id="mail_username"
                               value="{{ old('mail_username', $smtpConfig['mail_username']) }}"
                               placeholder="VD: luong51024@gmail.com">
                    </div>

                    <div class="field">
                        <label for="mail_password">Mật khẩu / App Password</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="password" name="mail_password" id="mail_password"
                                   value="{{ old('mail_password', $smtpConfig['mail_password']) }}"
                                   placeholder="Mật khẩu ứng dụng 16 ký tự" style="width: 100%; padding-right: 40px;">
                            <button type="button" id="togglePasswordBtn"
                                    style="position: absolute; right: 10px; background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; display: flex; align-items: center;"
                                    title="Ẩn/Hiện mật khẩu">
                                <svg id="togglePasswordIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <label for="mail_from_address">Email người gửi (From Address)</label>
                        <input type="email" name="mail_from_address" id="mail_from_address"
                               value="{{ old('mail_from_address', $smtpConfig['mail_from_address']) }}"
                               placeholder="no-reply@minicine.vn" required>
                    </div>

                    <div class="field">
                        <label for="mail_from_name">Tên hiển thị người gửi (From Name)</label>
                        <input type="text" name="mail_from_name" id="mail_from_name"
                               value="{{ old('mail_from_name', $smtpConfig['mail_from_name']) }}"
                               placeholder="Mini Cine" required>
                    </div>

                    <div class="field full" style="margin-top: 5px;">
                        <div style=" border: 1px solid var(--line); border-radius: 10px; padding: 14px 16px; font-size: 13px; color: var(--muted); line-height: 1.6;">
                            <strong style="color: var(--fg); display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Hướng dẫn cấu hình Gmail SMTP:
                            </strong>
                            <ol style="margin: 6px 0 0 18px;">
                                <li>Bật <strong>Xác minh 2 bước</strong> trong tài khoản Google.</li>
                                <li>Vào mục <em>Bảo mật &rarr; Mật khẩu ứng dụng (App passwords)</em>.</li>
                                <li>Tạo mật khẩu ứng dụng cho Mail và copy chuỗi 16 ký tự dán vào ô <strong>Password</strong> ở trên.</li>
                            </ol>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Lưu cấu hình SMTP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CỘT PHẢI: GỬI THỬ NGHIỆM & MẪU EMAIL --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            {{-- PANEL 2: GỬI THỬ NGHIỆM SMTP --}}
            <div class="panel">
                <div class="panel-head">
                    <h4>Kiểm tra kết nối SMTP (Gửi thử)</h4>
                </div>
                <div class="panel-body">
                    <p style="font-size: 13.5px; color: var(--muted); margin-bottom: 14px;">
                        Gửi một email thử nghiệm để kiểm tra xem cấu hình SMTP của bạn có kết nối và gửi thư thành công hay không.
                    </p>

                    <form id="testEmailForm" action="{{ route('admin.email.test') }}" method="POST">
                        @csrf
                        <div class="field" style="margin-bottom: 14px;">
                            <label for="test_email">Email người nhận thử nghiệm</label>
                            <input type="email" name="test_email" id="test_email"
                                   value="{{ old('test_email', $smtpConfig['mail_from_address']) }}"
                                   placeholder="Nhập email của bạn để nhận thử" required>
                        </div>

                        <div id="testResultBox" style="display:none; padding: 12px 14px; border-radius: 9px; font-size: 13px; margin-bottom: 14px;"></div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn ghost" id="sendTestBtn">
                                <span id="sendTestBtnText" style="display: inline-flex; align-items: center; gap: 6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Gửi email kiểm tra
                                </span>
                                <span id="sendTestSpinner" style="display:none;">Đang gửi kết nối...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PANEL 3: DANH SÁCH MẪU EMAIL --}}
            <div class="panel">
                <div class="panel-head">
                    <h4>Mẫu Email hệ thống</h4>
                </div>
                <div class="panel-body" style="padding: 10px 18px;">
                    <div class="email-tpl-item">
                        <div class="email-tpl-info">
                            <b>Thông báo người dùng (User Notification)</b>
                            <small>Mẫu gửi thông báo phim mới, bảo trì, ưu đãi, tin tức</small>
                        </div>
                        <button type="button" class="mini" onclick="openPreviewModal('user_notification', 'new_movie')" title="Xem trước mẫu">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    <div class="email-tpl-item">
                        <div class="email-tpl-info">
                            <b>Thông báo khóa tài khoản (Account Banned)</b>
                            <small>Tự động gửi khi tài khoản vi phạm và bị khóa</small>
                        </div>
                        <button type="button" class="mini" onclick="openPreviewModal('user_banned')" title="Xem trước mẫu">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    <div class="email-tpl-item">
                        <div class="email-tpl-info">
                            <b>Thông báo ưu đãi & khuyến mãi (Promotions)</b>
                            <small>Mẫu gửi khuyến mãi VIP / Gói Premium</small>
                        </div>
                        <button type="button" class="mini" onclick="openPreviewModal('user_notification', 'promotion')" title="Xem trước mẫu">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- MODAL PREVIEW MẪU EMAIL --}}
<div id="emailPreviewModal" class="email-modal" style="display:none;">
    <div class="email-modal-overlay" onclick="closePreviewModal()"></div>
    <div class="email-modal-dialog">
        <div class="email-modal-head">
            <div>
                <h4 id="previewModalTitle" style="margin: 0; font-size: 16px;">Xem trước mẫu Email</h4>
                <small style="color: var(--muted);">Giao diện thực tế hiển thị trên hộp thư của người dùng</small>
            </div>
            <div style="display: flex; gap: 8px; align-items: center;">
                <button type="button" class="btn ghost btn-size-toggle" onclick="togglePreviewSize('desktop')" id="btnSizeDesktop">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
                    </svg>
                    Desktop
                </button>
                <button type="button" class="btn ghost btn-size-toggle" onclick="togglePreviewSize('mobile')" id="btnSizeMobile">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" x2="12.01" y1="18" y2="18"/>
                    </svg>
                    Mobile
                </button>
                <button type="button" class="email-modal-close" onclick="closePreviewModal()">&times;</button>
            </div>
        </div>
        <div class="email-modal-body" id="previewModalBody">
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 580px; border: none; background: #0c0d12; border-radius: 8px; transition: width 0.3s ease;"></iframe>
        </div>
    </div>
</div>

<style>
.email-alert {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}
.email-alert.success {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}
.email-alert.error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.email-tpl-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--line);
}
.email-tpl-item:last-child {
    border-bottom: none;
}
.email-tpl-info b {
    display: block;
    font-size: 14px;
    color: var(--fg);
}
.email-tpl-info small {
    display: block;
    color: var(--muted);
    font-size: 12px;
    margin-top: 2px;
}

.btn-size-toggle {
    padding: 6px 12px;
    font-size: 12.5px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Modal Preview */
.email-modal {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.email-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
}
.email-modal-dialog {
    position: relative;
    width: 100%;
    max-width: 860px;
    background: var(--bg);
    border: 1px solid var(--line);
    border-radius: 14px;
    box-shadow: 0 20px 40px rgba(0,0,0,.4);
    z-index: 10;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.email-modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid var(--line);
    background: var(--panel);
}
.email-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--muted);
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
}
.email-modal-close:hover {
    color: var(--fg);
}
.email-modal-body {
    padding: 14px;
    display: flex;
    justify-content: center;
    background: #08090d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ẩn / hiện mật khẩu SMTP
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('mail_password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                if (toggleIcon) {
                    toggleIcon.innerHTML = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>';
                }
            } else {
                pwdInput.type = 'password';
                if (toggleIcon) {
                    toggleIcon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
                }
            }
        });
    }

    // Gửi thử nghiệm email qua AJAX
    const testForm = document.getElementById('testEmailForm');
    const testBtn = document.getElementById('sendTestBtn');
    const testBtnText = document.getElementById('sendTestBtnText');
    const testSpinner = document.getElementById('sendTestSpinner');
    const resultBox = document.getElementById('testResultBox');

    if (testForm) {
        testForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const testEmail = document.getElementById('test_email').value;
            if (!testEmail) return;

            testBtn.disabled = true;
            testBtnText.style.display = 'none';
            testSpinner.style.display = 'inline';
            resultBox.style.display = 'none';

            // Thu thập dữ liệu từ form SMTP để test runtime
            const formData = new FormData(testForm);
            const smtpForm = document.getElementById('smtpForm');
            if (smtpForm) {
                const sData = new FormData(smtpForm);
                for (const [key, val] of sData.entries()) {
                    if (key !== '_token') formData.append(key, val);
                }
            }

            try {
                const res = await fetch('{{ route('admin.email.test') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await res.json();

                resultBox.style.display = 'block';
                if (res.ok && data.success) {
                    resultBox.style.background = 'rgba(16, 185, 129, 0.15)';
                    resultBox.style.border = '1px solid rgba(16, 185, 129, 0.35)';
                    resultBox.style.color = '#10b981';
                    resultBox.innerHTML = '<strong>✓ ' + data.message + '</strong><br><small style="color:var(--fg); opacity:0.85;">Hệ thống đã gửi một bức thư kiểm tra tới hộp thư của bạn.</small>';
                } else {
                    resultBox.style.background = 'rgba(239, 68, 68, 0.15)';
                    resultBox.style.border = '1px solid rgba(239, 68, 68, 0.35)';
                    resultBox.style.color = '#ef4444';
                    resultBox.innerHTML = '<strong>✕ ' + (data.message || 'Lỗi gửi email kiểm tra.') + '</strong>';
                }
            } catch (err) {
                resultBox.style.display = 'block';
                resultBox.style.background = 'rgba(239, 68, 68, 0.15)';
                resultBox.style.border = '1px solid rgba(239, 68, 68, 0.35)';
                resultBox.style.color = '#ef4444';
                resultBox.innerHTML = '<strong>✕ Lỗi kết nối máy chủ: ' + err.message + '</strong>';
            } finally {
                testBtn.disabled = false;
                testBtnText.style.display = 'inline';
                testSpinner.style.display = 'none';
            }
        });
    }
});

function openPreviewModal(template, type = 'system') {
    const modal = document.getElementById('emailPreviewModal');
    const iframe = document.getElementById('previewIframe');
    if (!modal || !iframe) return;

    let url = '{{ route('admin.email.preview') }}?template=' + encodeURIComponent(template);
    if (type) url += '&type=' + encodeURIComponent(type);

    iframe.src = url;
    modal.style.display = 'flex';
}

function closePreviewModal() {
    const modal = document.getElementById('emailPreviewModal');
    const iframe = document.getElementById('previewIframe');
    if (modal) modal.style.display = 'none';
    if (iframe) iframe.src = 'about:blank';
}

function togglePreviewSize(size) {
    const iframe = document.getElementById('previewIframe');
    const btnDesktop = document.getElementById('btnSizeDesktop');
    const btnMobile = document.getElementById('btnSizeMobile');

    if (size === 'mobile') {
        iframe.style.width = '375px';
        btnMobile.style.background = 'var(--ink)';
        btnMobile.style.color = 'var(--ink-inv)';
        btnDesktop.style.background = '';
        btnDesktop.style.color = '';
    } else {
        iframe.style.width = '100%';
        btnDesktop.style.background = 'var(--ink)';
        btnDesktop.style.color = 'var(--ink-inv)';
        btnMobile.style.background = '';
        btnMobile.style.color = '';
    }
}
</script>
@endsection