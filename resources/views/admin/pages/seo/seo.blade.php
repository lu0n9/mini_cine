@extends('admin.layouts.master')

@section('content')
<section id="seo-page" class="page">
    <div class="page-head">
        <div>
            <h3>Cài đặt SEO toàn trang</h3>
            <p>Tối ưu hóa công cụ tìm kiếm (Google, Bing), cấu hình Meta Tags, Open Graph và chỉ mục robots toàn hệ thống.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.sitemap') }}" class="btn ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                </svg>
                Quản lý Sitemap
            </a>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="notify-alert success">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="notify-alert error">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="notify-alert error">
            <strong>Vui lòng kiểm tra lại các trường thông tin:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================================
        STATISTICS CARDS (ĐỒNG BỘ THIẾT KẾ CHUẨN)
    ========================================================== --}}
    <div class="stats">
        {{-- Trạng thái Robots --}}
        <div class="stat">
            <span class="label">Trạng thái Robots</span>
            <div class="num" style="font-size: 22px;">{{ strtoupper($setting->robots_meta ?: 'INDEX, FOLLOW') }}</div>
        </div>

        {{-- Google Verification --}}
        <div class="stat">
            <span class="label">Google Verification</span>
            <div class="num" style="font-size: 22px;">
                @if(!empty($setting->google_verification))
                    ĐÃ CẤU HÌNH
                @else
                    CHƯA CÓ
                @endif
            </div>
        </div>

        {{-- Bing Verification --}}
        <div class="stat">
            <span class="label">Bing Verification</span>
            <div class="num" style="font-size: 22px;">
                @if(!empty($setting->bing_verification))
                    ĐÃ CẤU HÌNH
                @else
                    CHƯA CÓ
                @endif
            </div>
        </div>

        {{-- Canonical Domain --}}
        <div class="stat">
            <span class="label">Canonical Host</span>
            <div class="num" style="font-size: 22px;">{{ parse_url($setting->canonical_url, PHP_URL_HOST) ?: 'MINICINE' }}</div>
        </div>
    </div>

    {{-- =========================================================
        XEM TRƯỚC KẾT QUẢ TÌM KIẾM GOOGLE (LIVE SERP PREVIEW)
    ========================================================== --}}
    <div class="panel" style="margin-bottom: 26px;">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Xem trước kết quả tìm kiếm Google (SERP Preview)</h4>
            <span style="font-size: 13px; color: var(--muted);">Mô phỏng hiển thị trên trang tìm kiếm</span>
        </div>
        <div class="panel-body">
            <div class="google-preview-box">
                <div class="google-preview-header">
                    <div class="google-favicon">
                        @if(!empty($setting->favicon) && file_exists(public_path($setting->favicon)))
                            <img src="{{ asset($setting->favicon) }}" alt="Favicon" style="width: 18px; height: 18px; border-radius: 4px; object-fit: contain;">
                        @else
                            <div style="width: 18px; height: 18px; background: #e50914; color: #fff; font-size: 11px; font-weight: bold; border-radius: 4px; display: grid; place-items: center;">C</div>
                        @endif
                    </div>
                    <div class="google-url-info">
                        <span class="google-site-name">Mini Cine</span>
                        <span class="google-url" id="previewUrl">{{ $setting->canonical_url ?: config('app.url', 'https://minicine.vn') }}</span>
                    </div>
                </div>
                <div class="google-title" id="previewTitle">
                    {{ $setting->site_title ?: 'MINI CINE — Xem phim trực tuyến chất lượng 4K' }}
                </div>
                <div class="google-desc" id="previewDesc">
                    {{ $setting->site_description ?: 'MINI CINE: nền tảng xem phim trực tuyến với phim lẻ, phim bộ, phim chiếu rạp chất lượng 4K HDR, phụ đề Việt và thuyết minh.' }}
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
        FORM CẤU HÌNH SEO
    ========================================================== --}}
    <div class="panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Cấu hình thẻ Meta & Thuộc tính SEO</h4>
            <span style="font-size: 13px; color: var(--muted);">Áp dụng tự động cho toàn website</span>
        </div>

        <div class="panel-body">
            <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data" class="form-grid" id="seoForm">
                @csrf

                {{-- 1. Site Title --}}
                <div class="field full">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label for="site_title">Tiêu đề trang web (Site Title / Meta Title) <span style="color:#ef4444;">*</span></label>
                        <span id="titleCounter" style="font-size: 12px; color: var(--muted);">0 / 65 ký tự</span>
                    </div>
                    <input type="text" name="site_title" id="site_title"
                           value="{{ old('site_title', $setting->site_title) }}"
                           placeholder="VD: MINI CINE — Xem phim trực tuyến chất lượng 4K" required>
                </div>

                {{-- 2. Canonical URL --}}
                <div class="field">
                    <label for="canonical_url">Canonical URL (Tên miền chính)</label>
                    <input type="url" name="canonical_url" id="canonical_url"
                           value="{{ old('canonical_url', $setting->canonical_url) }}"
                           placeholder="VD: https://minicine.vn">
                </div>

                {{-- 3. Robots Meta --}}
                <div class="field">
                    <label for="robots_meta">Chỉ mục công cụ tìm kiếm (Robots Meta)</label>
                    <select name="robots_meta" id="robots_meta">
                        <option value="index, follow" {{ old('robots_meta', $setting->robots_meta) === 'index, follow' ? 'selected' : '' }}>index, follow (Cho phép tìm kiếm & lập chỉ mục - Khuyên dùng)</option>
                        <option value="noindex, nofollow" {{ old('robots_meta', $setting->robots_meta) === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (Chặn toàn bộ công cụ tìm kiếm)</option>
                        <option value="index, nofollow" {{ old('robots_meta', $setting->robots_meta) === 'index, nofollow' ? 'selected' : '' }}>index, nofollow (Cho phép lập chỉ mục nhưng không theo dõi liên kết)</option>
                        <option value="noindex, follow" {{ old('robots_meta', $setting->robots_meta) === 'noindex, follow' ? 'selected' : '' }}>noindex, follow (Không lập chỉ mục nhưng theo dõi liên kết)</option>
                    </select>
                </div>

                {{-- 4. Site Description --}}
                <div class="field full">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label for="site_description">Mô tả website (Meta Description)</label>
                        <span id="descCounter" style="font-size: 12px; color: var(--muted);">0 / 160 ký tự</span>
                    </div>
                    <textarea name="site_description" id="site_description" rows="3"
                              placeholder="Nhập mô tả ngắn gọn và hấp dẫn về website của bạn (khoảng 140 - 160 ký tự là tối ưu nhất cho Google)...">{{ old('site_description', $setting->site_description) }}</textarea>
                </div>

                {{-- 5. Keywords --}}
                <div class="field full">
                    <label for="keywords">Từ khóa chính (Meta Keywords - Phân cách bằng dấu phẩy)</label>
                    <input type="text" name="keywords" id="keywords"
                           value="{{ old('keywords', $setting->keywords) }}"
                           placeholder="VD: xem phim, phim online, phim hd, phim chiếu rạp, mini cine, phim vietsub">
                </div>

                {{-- 6. Google Verification --}}
                <div class="field">
                    <label for="google_verification">Google Search Console Verification Code</label>
                    <input type="text" name="google_verification" id="google_verification"
                           value="{{ old('google_verification', $setting->google_verification) }}"
                           placeholder="Mã thẻ meta google-site-verification">
                </div>

                {{-- 7. Bing Verification --}}
                <div class="field">
                    <label for="bing_verification">Bing Webmaster Tools Verification Code</label>
                    <input type="text" name="bing_verification" id="bing_verification"
                           value="{{ old('bing_verification', $setting->bing_verification) }}"
                           placeholder="Mã thẻ msvalidate.01">
                </div>

                {{-- 8. Favicon Upload --}}
                <div class="field">
                    <label for="favicon">Favicon website (Hỗ trợ .ico, .png, .svg)</label>
                    <div style="display: flex; gap: 12px; align-items: center; margin-top: 4px;">
                        @if(!empty($setting->favicon) && file_exists(public_path($setting->favicon)))
                            <div style="width: 40px; height: 40px; border: 1px solid var(--line); border-radius: 8px; display: grid; place-items: center; background: var(--panel-2);">
                                <img src="{{ asset($setting->favicon) }}" alt="Favicon hiện tại" style="max-width: 28px; max-height: 28px;">
                            </div>
                        @endif
                        <input type="file" name="favicon" id="favicon" accept=".ico,.png,.svg,.jpg,.jpeg" style="flex: 1;">
                    </div>
                </div>

                {{-- 9. Social Share Image (OG Image) --}}
                <div class="field">
                    <label for="og_image">Ảnh xem trước mạng xã hội (Open Graph Image - 1200x630)</label>
                    <div style="display: flex; gap: 12px; align-items: center; margin-top: 4px;">
                        @if(!empty($setting->og_image) && file_exists(public_path($setting->og_image)))
                            <div style="width: 60px; height: 40px; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; background: var(--panel-2);">
                                <img src="{{ asset($setting->og_image) }}" alt="OG Image hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="og_image" id="og_image" accept="image/*" style="flex: 1;">
                    </div>
                </div>

                {{-- 10. Custom Robots.txt --}}
                <div class="field full">
                    <label for="custom_robots_txt">Nội dung tệp robots.txt tùy chỉnh (Tự động cập nhật vào public/robots.txt)</label>
                    <textarea name="custom_robots_txt" id="custom_robots_txt" rows="5"
                              style="font-family: monospace; font-size: 13px;"
                              placeholder="User-agent: *&#10;Allow: /&#10;Disallow: /admin/&#10;&#10;Sitemap: {{ url('/sitemap.xml') }}">{{ old('custom_robots_txt', $setting->custom_robots_txt) }}</textarea>
                </div>

                {{-- 11. Form Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Lưu cài đặt SEO
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.notify-alert {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}
.notify-alert.success {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}
.notify-alert.error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

/* Google SERP Preview Box */
.google-preview-box {
    background: #1f1f23;
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 18px 22px;
    max-width: 680px;
}
.google-preview-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.google-url-info {
    display: flex;
    flex-direction: column;
    line-height: 1.25;
}
.google-site-name {
    font-size: 13px;
    font-weight: 500;
    color: var(--fg);
}
.google-url {
    font-size: 11.5px;
    color: var(--muted);
    word-break: break-all;
}
.google-title {
    font-size: 18px;
    font-weight: 600;
    color: #8ab4f8;
    margin-bottom: 6px;
    line-height: 1.35;
    cursor: pointer;
}
.google-title:hover {
    text-decoration: underline;
}
.google-desc {
    font-size: 13.5px;
    color: #bdc1c6;
    line-height: 1.5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('site_title');
    const descInput = document.getElementById('site_description');
    const urlInput = document.getElementById('canonical_url');

    const previewTitle = document.getElementById('previewTitle');
    const previewDesc = document.getElementById('previewDesc');
    const previewUrl = document.getElementById('previewUrl');

    const titleCounter = document.getElementById('titleCounter');
    const descCounter = document.getElementById('descCounter');

    function updatePreview() {
        if (titleInput && previewTitle) {
            const val = titleInput.value.trim();
            previewTitle.textContent = val || 'MINI CINE — Xem phim trực tuyến chất lượng 4K';
            if (titleCounter) {
                const len = val.length;
                titleCounter.textContent = len + ' / 65 ký tự' + (len > 65 ? ' (Hơi dài)' : '');
                titleCounter.style.color = len > 65 ? '#ef4444' : 'var(--muted)';
            }
        }

        if (descInput && previewDesc) {
            const val = descInput.value.trim();
            previewDesc.textContent = val || 'MINI CINE: nền tảng xem phim trực tuyến với phim lẻ, phim bộ, phim chiếu rạp chất lượng 4K HDR, phụ đề Việt và thuyết minh.';
            if (descCounter) {
                const len = val.length;
                descCounter.textContent = len + ' / 160 ký tự' + (len > 160 ? ' (Hơi dài)' : '');
                descCounter.style.color = len > 160 ? '#ef4444' : 'var(--muted)';
            }
        }

        if (urlInput && previewUrl) {
            previewUrl.textContent = urlInput.value.trim() || '{{ config('app.url', 'https://minicine.vn') }}';
        }
    }

    if (titleInput) titleInput.addEventListener('input', updatePreview);
    if (descInput) descInput.addEventListener('input', updatePreview);
    if (urlInput) urlInput.addEventListener('input', updatePreview);

    updatePreview();
});
</script>
@endsection