@extends('admin.layouts.master')

@section('content')
<section id="sitemap-page" class="page">
    <div class="page-head">
        <div>
            <h3>Quản lý Sitemap (XML)</h3>
            <p>Tạo và quản lý các tệp sitemap XML hỗ trợ Google, Bing lập chỉ mục phim, tập phim, thể loại và trang tĩnh.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                Xem sitemap.xml
            </a>
            <form action="{{ route('admin.sitemap.generate-all') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    Generate toàn bộ
                </button>
            </form>
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

    {{-- =========================================================
        STATISTICS CARDS (ĐỒNG BỘ THIẾT KẾ CHUẨN)
    ========================================================== --}}
    <div class="stats">
        {{-- Tổng số URL --}}
        <div class="stat">
            <span class="label">Tổng số URL</span>
            <div class="num">{{ number_format($totalUrls) }}</div>
        </div>

        {{-- Số tệp sitemap --}}
        <div class="stat">
            <span class="label">Tệp Sitemap</span>
            <div class="num">{{ count($sitemaps) }} file</div>
        </div>

        {{-- Lần cập nhật gần nhất --}}
        <div class="stat">
            <span class="label">Cập nhật gần nhất</span>
            <div class="num" style="font-size: 22px;">{{ $lastGenerated }}</div>
        </div>

        {{-- Trạng thái Sitemap Index --}}
        <div class="stat">
            <span class="label">Sitemap Index</span>
            <div class="num" style="font-size: 22px;">
                @if($mainExists)
                    HOẠT ĐỘNG
                @else
                    CHƯA TẠO
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================
        BẢNG DANH SÁCH SITEMAP
    ========================================================== --}}
    <div class="panel" style="margin-bottom: 26px;">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Danh sách tệp Sitemap XML</h4>
            <span style="font-size: 13px; color: var(--muted);">Tự động phân nhánh chuẩn sitemapindex</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sitemap</th>
                    <th>Đường dẫn tệp XML</th>
                    <th>Số URL ước tính</th>
                    <th>Dung lượng</th>
                    <th>Cập nhật</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right; width: 90px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sitemaps as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['name'] }}</strong>
                        </td>
                        <td>
                            <code class="sitemap-url-code">/{{ $item['file_name'] }}</code>
                        </td>
                        <td style="font-weight: 600;">
                            {{ number_format($item['count']) }} URL
                        </td>
                        <td style="color: var(--muted); font-size: 13px;">
                            {{ $item['size'] }}
                        </td>
                        <td style="color: var(--muted); font-size: 12.5px;">
                            {{ $item['updated_at'] }}
                        </td>
                        <td>
                            @if($item['exists'])
                                <span class="status">Hoạt động</span>
                            @else
                                <span class="status off">Chưa tạo</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions" style="justify-content: flex-end;">
                                {{-- Nút tạo lại riêng lẻ --}}
                                <form action="{{ route('admin.sitemap.generate-single', $item['type']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="mini" title="Tạo lại sitemap này">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                        </svg>
                                    </button>
                                </form>

                                {{-- Nút mở file XML --}}
                                <a href="{{ $item['url'] }}" target="_blank" class="mini" title="Xem XML trực tiếp">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- =========================================================
        HƯỚNG DẪN KHAI BÁO GOOGLE SEARCH CONSOLE
    ========================================================== --}}
    <div class="panel">
        <div class="panel-head">
            <h4>Khai báo Sitemap với Google Search Console</h4>
        </div>
        <div class="panel-body">
            <p style="font-size: 13.5px; color: var(--muted); margin-bottom: 14px; line-height: 1.6;">
                Để các công cụ tìm kiếm thu thập toàn bộ phim và nội dung nhanh nhất, hãy copy đường dẫn bên dưới và dán vào mục <strong>Sitemaps</strong> trong <em>Google Search Console</em> hoặc <em>Bing Webmaster Tools</em>:
            </p>

            <div style="display: flex; gap: 10px; align-items: center; max-width: 600px;">
                <input type="text" id="sitemapIndexUrl" value="{{ url('/sitemap.xml') }}" readonly style="font-family: monospace; font-size: 13.5px; flex: 1;">
                <button type="button" class="btn ghost" onclick="copySitemapUrl()" id="copyBtn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                    </svg>
                    <span id="copyBtnText">Sao chép URL</span>
                </button>
            </div>
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

.sitemap-url-code {
    font-size: 12px;
    background: var(--panel-2);
    border: 1px solid var(--line);
    padding: 3px 8px;
    border-radius: 6px;
    color: var(--fg);
}
</style>

<script>
function copySitemapUrl() {
    const input = document.getElementById('sitemapIndexUrl');
    const copyText = document.getElementById('copyBtnText');
    if (!input) return;

    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        if (copyText) copyText.textContent = 'Đã chép!';
        setTimeout(() => {
            if (copyText) copyText.textContent = 'Sao chép URL';
        }, 2000);
    });
}
</script>
@endsection