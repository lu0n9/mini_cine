@extends('admin.layouts.master')

@section('content')
<section id="notifications-page" class="page">
    <div class="page-head">
        <div>
            <h3>Quản lý & Gửi thông báo</h3>
            <p>Gửi thông báo qua Email SMTP và hệ thống thông báo trên Web tới tất cả thành viên, từng người dùng hoặc theo thời gian đăng ký.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.email') }}" class="btn ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 7 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7H1a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 2.6 7"/>
                </svg>
                Cấu hình Email SMTP
            </a>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="notify-alert success">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="notify-alert warning">
            <span>! {{ session('warning') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="notify-alert error">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="notify-alert error">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================================
        STATISTICS CARDS (ĐỒNG BỘ CSS VỚI MẪU)
    ========================================================== --}}
    <div class="stats">
        {{-- Tổng người dùng --}}
        <div class="stat">
            <span class="label">Tổng người dùng</span>
            <div class="num">{{ number_format($totalUsers) }}</div>
        </div>

        {{-- Đăng ký 7 ngày qua --}}
        <div class="stat">
            <span class="label">Đăng ký 7 ngày qua</span>
            <div class="num">{{ number_format($users7Days) }}</div>
        </div>

        {{-- Đăng ký 30 ngày qua --}}
        <div class="stat">
            <span class="label">Đăng ký 30 ngày qua</span>
            <div class="num">{{ number_format($users30Days) }}</div>
        </div>

        {{-- Chiến dịch đã gửi --}}
        <div class="stat">
            <span class="label">Chiến dịch đã gửi</span>
            <div class="num">{{ number_format($campaigns->total()) }}</div>
        </div>
    </div>

    {{-- FORM TẠO & GỬI THÔNG BÁO --}}
    <div class="panel" style="margin-bottom: 26px;">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Tạo và gửi thông báo</h4>
            <span style="font-size: 13px; color: var(--muted);">Email SMTP & Web Notification</span>
        </div>

        <div class="panel-body">
            <form action="{{ route('admin.notifications.send') }}" method="POST" id="notificationForm" class="form-grid">
                @csrf

                {{-- 1. TIÊU ĐỀ --}}
                <div class="field full">
                    <label for="title">Tiêu đề thông báo / Chủ đề Email <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title') }}"
                           placeholder="VD: Tập 12 Crimson Vale đã lên sóng trọn bộ 4K HDR" required>
                </div>

                {{-- 2. LOẠI THÔNG BÁO --}}
                <div class="field">
                    <label for="type">Loại thông báo</label>
                    <select name="type" id="type">
                        <option value="system" {{ old('type') === 'system' ? 'selected' : '' }}>Thông báo hệ thống</option>
                        <option value="new_movie" {{ old('type') === 'new_movie' ? 'selected' : '' }}>Phim mới / Tập mới lên sóng</option>
                        <option value="promotion" {{ old('type') === 'promotion' ? 'selected' : '' }}>Ưu đãi & Khuyến mãi</option>
                        <option value="maintenance" {{ old('type') === 'maintenance' ? 'selected' : '' }}>Bảo trì hệ thống</option>
                        <option value="account" {{ old('type') === 'account' ? 'selected' : '' }}>Thông tin tài khoản</option>
                    </select>
                </div>

                {{-- 3. KÊNH GỬI THÔNG BÁO --}}
                <div class="field">
                    <label>Phương thức gửi thông báo <span style="color:#ef4444;">*</span></label>
                    <div style="display: flex; gap: 20px; align-items: center; padding-top: 8px;">
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: normal;">
                            <input type="checkbox" name="channels[]" value="email" checked style="width: 16px; height: 16px; accent-color: var(--ink);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            Gửi Email SMTP
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: normal;">
                            <input type="checkbox" name="channels[]" value="web" checked style="width: 16px; height: 16px; accent-color: var(--ink);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            Thông báo trên Web
                        </label>
                    </div>
                </div>

                {{-- 4. ĐỐI TƯỢNG NGƯỜI NHẬN --}}
                <div class="field full" style="margin-top: 6px;">
                    <label>Đối tượng người nhận <span style="color:#ef4444;">*</span></label>

                    {{-- SEGMENTED TABS --}}
                    <div class="target-type-selector">
                        <label class="target-tab">
                            <input type="radio" name="target_type" value="all" {{ old('target_type', 'all') === 'all' ? 'checked' : '' }} onchange="onTargetTypeChange('all')">
                            <span>Tất cả người dùng ({{ $totalUsers }})</span>
                        </label>
                        <label class="target-tab">
                            <input type="radio" name="target_type" value="single" {{ old('target_type') === 'single' ? 'checked' : '' }} onchange="onTargetTypeChange('single')">
                            <span>Từng người dùng cụ thể</span>
                        </label>
                        <label class="target-tab">
                            <input type="radio" name="target_type" value="date_range" {{ old('target_type') === 'date_range' ? 'checked' : '' }} onchange="onTargetTypeChange('date_range')">
                            <span>Theo ngày đăng ký tài khoản</span>
                        </label>
                    </div>

                    {{-- NỘI DUNG TARGET: ALL --}}
                    <div id="targetAllBox" class="target-content-box" style="{{ old('target_type', 'all') === 'all' ? '' : 'display:none;' }}">
                        <div style="font-size: 13.5px; color: var(--muted);">
                            Thông báo sẽ được gửi đồng loạt tới toàn bộ <strong>{{ number_format($totalUsers) }} người dùng</strong> đang có trong cơ sở dữ liệu.
                        </div>
                    </div>

                    {{-- NỘI DUNG TARGET: SINGLE / SPECIFIC USERS --}}
                    <div id="targetSingleBox" class="target-content-box" style="{{ old('target_type') === 'single' ? '' : 'display:none;' }}">
                        <div style="position: relative;">
                            <div style="display:flex; align-items:center; position:relative;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; left:12px; color:var(--muted); pointer-events:none;">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <input type="text" id="userSearchInput" placeholder="Tìm kiếm người dùng theo tên hoặc email..." autocomplete="off" style="padding-left: 36px;">
                            </div>
                            <div id="userSearchResults" class="user-search-dropdown" style="display:none;"></div>
                        </div>

                        {{-- GỢI Ý NGƯỜI DÙNG NHANH (SUGGESTIONS) --}}
                        @if(isset($suggestedUsers) && $suggestedUsers->isNotEmpty())
                            <div class="user-suggestions-block">
                                <span class="suggestion-title">Gợi ý người dùng:</span>
                                <div class="suggestion-chips">
                                    @foreach($suggestedUsers as $sUser)
                                        <button type="button" class="suggestion-chip" id="suggestBtn_{{ $sUser->id }}" onclick="addUserToSelection({{ $sUser->id }}, '{{ addslashes($sUser->name) }}', '{{ addslashes($sUser->email) }}')">
                                            + {{ $sUser->name }} <small style="color:var(--muted);">({{ $sUser->email }})</small>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- DANH SÁCH USER ĐÃ CHỌN --}}
                        <div style="margin-top: 14px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 6px;">
                                <span style="font-size: 12.5px; font-weight:600; color: var(--muted); text-transform:uppercase; letter-spacing:0.5px;">Người dùng đã chọn:</span>
                                <button type="button" class="clear-all-btn" id="clearAllUsersBtn" style="display:none;" onclick="clearAllSelectedUsers()">Xóa tất cả</button>
                            </div>
                            <div id="selectedUsersContainer" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                            <div id="hiddenInputsContainer"></div>
                        </div>
                    </div>

                    {{-- NỘI DUNG TARGET: DATE RANGE --}}
                    <div id="targetDateBox" class="target-content-box" style="{{ old('target_type') === 'date_range' ? '' : 'display:none;' }}">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
                            <div>
                                <label style="font-size: 12.5px; margin-bottom: 4px; display:block;">Mốc thời gian định sẵn</label>
                                <select name="date_preset" id="datePreset" onchange="onPresetChange(this.value)">
                                    <option value="7_days" {{ old('date_preset', '7_days') === '7_days' ? 'selected' : '' }}>Đăng ký trong 7 ngày gần đây</option>
                                    <option value="30_days" {{ old('date_preset') === '30_days' ? 'selected' : '' }}>Đăng ký trong 30 ngày gần đây</option>
                                    <option value="90_days" {{ old('date_preset') === '90_days' ? 'selected' : '' }}>Đăng ký trong 90 ngày gần đây</option>
                                    <option value="year_to_date" {{ old('date_preset') === 'year_to_date' ? 'selected' : '' }}>Đăng ký từ đầu năm nay (YTD)</option>
                                    <option value="custom" {{ old('date_preset') === 'custom' ? 'selected' : '' }}>Khoảng ngày tùy chọn...</option>
                                </select>
                            </div>

                            <div id="customDateRangeBox" style="{{ old('date_preset') === 'custom' ? 'display:flex;' : 'display:none;' }} gap: 8px;">
                                <div style="flex: 1;">
                                    <label style="font-size: 12.5px; margin-bottom: 4px; display:block;">Từ ngày</label>
                                    <input type="date" name="date_from" id="dateFrom" value="{{ old('date_from', now()->subDays(30)->format('Y-m-d')) }}" onchange="fetchUserCount()">
                                </div>
                                <div style="flex: 1;">
                                    <label style="font-size: 12.5px; margin-bottom: 4px; display:block;">Đến ngày</label>
                                    <input type="date" name="date_to" id="dateTo" value="{{ old('date_to', now()->format('Y-m-d')) }}" onchange="fetchUserCount()">
                                </div>
                            </div>
                        </div>

                        {{-- REALTIME RECIPIENT COUNT BADGE --}}
                        <div id="dateRangeEstimateBadge" style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: var(--panel-2); border: 1px solid var(--line); border-radius: 20px; font-size: 13px; color: var(--fg);">
                            <span id="estimateSpinner" style="display:none;">...</span>
                            <span id="estimateText">Đang tính toán số lượng...</span>
                        </div>
                    </div>
                </div>

                {{-- 5. NÚT KÊU GỌI HÀNH ĐỘNG (CTA) --}}
                <div class="field">
                    <label for="action_text">Nhãn nút bấm (Tùy chọn)</label>
                    <input type="text" name="action_text" id="action_text"
                           value="{{ old('action_text') }}"
                           placeholder="VD: Xem phim ngay / Nhận ưu đãi">
                </div>

                <div class="field">
                    <label for="action_url">Đường dẫn liên kết (CTA URL)</label>
                    <input type="url" name="action_url" id="action_url"
                           value="{{ old('action_url') }}"
                           placeholder="VD: http://localhost:8000/movies/ten-phim">
                </div>

                {{-- 6. NỘI DUNG THÔNG BÁO --}}
                <div class="field full">
                    <label for="message">Nội dung thông báo chi tiết <span style="color:#ef4444;">*</span></label>
                    <textarea name="message" id="message" rows="6"
                              placeholder="Nhập nội dung thông báo gửi tới người dùng. Hỗ trợ xuống dòng và định dạng văn bản..."
                              required>{{ old('message') }}</textarea>
                </div>

                {{-- 7. THAO TÁC FORM --}}
                <div class="form-actions">
                    <button type="button" class="btn ghost" onclick="openLivePreviewFromForm()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                        Xem trước Email
                    </button>
                    <button type="submit" class="btn" id="submitNotifyBtn" onclick="return confirmSendNotification()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        Gửi thông báo ngay
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
        BẢNG LỊCH SỬ THÔNG BÁO (CSS TƯƠNG TỰ BẢNG HOMEPAGE)
    ========================================================== --}}
    <div class="panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Lịch sử gửi thông báo</h4>
            <span style="font-size: 13px; color: var(--muted);">{{ $campaigns->total() }} chiến dịch</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tiêu đề thông báo</th>
                    <th>Loại</th>
                    <th>Đối tượng nhận</th>
                    <th>Số lượng</th>
                    <th>Kênh</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $camp)
                    <tr>
                        <td>
                            <strong>{{ $camp->title }}</strong>
                            @if($camp->action_url)
                                <div style="font-size: 11.5px; color: var(--muted); margin-top: 2px;">
                                    <a href="{{ $camp->action_url }}" target="_blank" style="color: var(--fg); text-decoration: underline;">
                                        {{ $camp->action_text ?: 'Liên kết' }}
                                    </a>
                                </div>
                            @endif
                        </td>

                        <td>
                            {{ $camp->type_label }}
                        </td>

                        <td style="color: var(--muted); font-size: 13px;">
                            {{ $camp->target_description ?: ucfirst($camp->target_type) }}
                        </td>

                        <td style="font-weight: 600;">
                            {{ number_format($camp->recipient_count) }} user
                        </td>

                        <td>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--ink);">
                                @if($camp->channel_email)
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" title="Email"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                @endif
                                @if($camp->channel_web)
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" title="Thông báo Web"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                @endif
                            </div>
                        </td>

                        <td>
                            @if($camp->status === 'success')
                                <span class="status">Thành công</span>
                            @elseif($camp->status === 'partial')
                                <span class="status warn">Một phần</span>
                            @else
                                <span class="status off">Thất bại</span>
                            @endif
                        </td>

                        <td style="color: var(--muted); font-size: 12.5px;">
                            {{ $camp->created_at->format('d/m/Y H:i') }}
                            @if($camp->admin)
                                <small style="display:block; color: var(--muted);">Bởi: {{ $camp->admin->name }}</small>
                            @endif
                        </td>

                        <td>
                            <div class="row-actions">
                                <form action="{{ route('admin.notifications.campaigns.delete', $camp->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi lịch sử này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mini" title="Xóa lịch sử">✕</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--muted); padding: 30px;">
                            Chưa có chiến dịch thông báo nào được gửi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PHÂN TRANG --}}
    @if($campaigns->hasPages())
        <div class="pagination">
            {{ $campaigns->links('pagination::custom') }}
        </div>
    @endif
</section>

{{-- MODAL PREVIEW MẪU EMAIL --}}
<div id="emailPreviewModal" class="notify-modal" style="display:none;">
    <div class="notify-modal-overlay" onclick="closePreviewModal()"></div>
    <div class="notify-modal-dialog">
        <div class="notify-modal-head">
            <div>
                <h4 style="margin: 0; font-size: 16px;">Xem trước mẫu Email</h4>
                <small style="color: var(--muted);">Nội dung hiển thị trên hộp thư của người dùng</small>
            </div>
            <button type="button" class="notify-modal-close" onclick="closePreviewModal()">&times;</button>
        </div>
        <div class="notify-modal-body">
            <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 560px; border: none; background: #0c0d12; border-radius: 8px;"></iframe>
        </div>
    </div>
</div>

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
.notify-alert.warning {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}
.notify-alert.error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

/* Target selector tabs */
.target-type-selector {
    display: flex;
    gap: 8px;
    background: var(--panel-2);
    padding: 5px;
    border-radius: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.target-tab {
    cursor: pointer;
    flex: 1;
    min-width: 180px;
}
.target-tab input {
    display: none;
}
.target-tab span {
    display: block;
    text-align: center;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--muted);
    transition: all .2s ease;
}
.target-tab input:checked + span {
    background: var(--bg);
    color: var(--fg);
    box-shadow: 0 2px 6px rgba(0,0,0,.08);
}

.target-content-box {
    background: var(--panel);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 16px;
}

/* User suggestions */
.user-suggestions-block {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed var(--line);
}
.suggestion-title {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}
.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.suggestion-chip {
    background: var(--bg);
    border: 1px solid var(--line);
    color: var(--fg);
    border-radius: 999px;
    padding: 4px 11px;
    font-size: 12.5px;
    cursor: pointer;
    transition: all .15s ease;
}
.suggestion-chip:hover {
    background: var(--panel-2);
    border-color: var(--ink);
}
.suggestion-chip.added {
    opacity: 0.5;
    pointer-events: none;
    text-decoration: line-through;
}

/* User search dropdown */
.user-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg);
    border: 1px solid var(--line);
    border-radius: 8px;
    margin-top: 4px;
    max-height: 220px;
    overflow-y: auto;
    z-index: 50;
    box-shadow: 0 10px 25px rgba(0,0,0,.12);
}
.user-search-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border-bottom: 1px solid var(--line);
    cursor: pointer;
    font-size: 13.5px;
}
.user-search-item:last-child {
    border-bottom: none;
}
.user-search-item:hover {
    background: var(--panel-2);
}

/* Selected user chips */
.user-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--ink);
    color: var(--ink-inv);
    font-size: 12.5px;
    padding: 4px 12px;
    border-radius: 20px;
}
.user-chip-remove {
    cursor: pointer;
    font-size: 13px;
    opacity: 0.7;
    margin-left: 2px;
}
.user-chip-remove:hover {
    opacity: 1;
}

.clear-all-btn {
    background: none;
    border: none;
    font-size: 12px;
    color: var(--muted);
    cursor: pointer;
    text-decoration: underline;
}
.clear-all-btn:hover {
    color: var(--ink);
}

/* Modal */
.notify-modal {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.notify-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
}
.notify-modal-dialog {
    position: relative;
    width: 100%;
    max-width: 860px;
    background: var(--bg);
    border: 1px solid var(--line);
    border-radius: 14px;
    z-index: 10;
    overflow: hidden;
}
.notify-modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid var(--line);
    background: var(--panel);
}
.notify-modal-close {
    background: none;
    border: none;
    font-size: 22px;
    color: var(--muted);
    cursor: pointer;
}
.notify-modal-body {
    padding: 14px;
    display: flex;
    justify-content: center;
    background: #08090d;
}
</style>

<script>
let selectedUsers = [];

function onTargetTypeChange(type) {
    document.getElementById('targetAllBox').style.display = type === 'all' ? 'block' : 'none';
    document.getElementById('targetSingleBox').style.display = type === 'single' ? 'block' : 'none';
    document.getElementById('targetDateBox').style.display = type === 'date_range' ? 'block' : 'none';

    if (type === 'date_range') {
        fetchUserCount();
    }
}

function onPresetChange(val) {
    const customBox = document.getElementById('customDateRangeBox');
    if (val === 'custom') {
        customBox.style.display = 'flex';
    } else {
        customBox.style.display = 'none';
    }
    fetchUserCount();
}

async function fetchUserCount() {
    const preset = document.getElementById('datePreset').value;
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;

    const spinner = document.getElementById('estimateSpinner');
    const text = document.getElementById('estimateText');

    spinner.style.display = 'inline';
    text.textContent = 'Đang tính toán số lượng...';

    try {
        let url = '{{ route('admin.notifications.user-count') }}?target_type=date_range&date_preset=' + encodeURIComponent(preset);
        if (preset === 'custom') {
            url += '&date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
        }

        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        text.innerHTML = 'Tìm thấy <strong>' + data.count + ' người dùng</strong> (' + data.description + ')';
    } catch (e) {
        text.textContent = 'Không thể tính toán số lượng';
    } finally {
        spinner.style.display = 'none';
    }
}

// Tìm kiếm người dùng cho Single target
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('userSearchInput');
    const resultsBox = document.getElementById('userSearchResults');

    let searchTimeout = null;

    if (searchInput && resultsBox) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim();
            clearTimeout(searchTimeout);

            if (query.length < 1) {
                resultsBox.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(async function () {
                try {
                    const res = await fetch('{{ route('admin.notifications.search-users') }}?q=' + encodeURIComponent(query), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const users = await res.json();

                    if (users.length === 0) {
                        resultsBox.innerHTML = '<div style="padding: 10px 14px; font-size: 13px; color: var(--muted);">Không tìm thấy người dùng phù hợp.</div>';
                    } else {
                        resultsBox.innerHTML = users.map(u => `
                            <div class="user-search-item" onclick="addUserToSelection(${u.id}, '${escapeHtml(u.name)}', '${escapeHtml(u.email)}')">
                                <div>
                                    <strong>${escapeHtml(u.name)}</strong>
                                    <small style="color: var(--muted); margin-left: 6px;">(${escapeHtml(u.email)})</small>
                                </div>
                                <span style="font-size: 12px; color: var(--muted);">Ngày tạo: ${u.created_at}</span>
                            </div>
                        `).join('');
                    }
                    resultsBox.style.display = 'block';
                } catch (e) {
                    console.error(e);
                }
            }, 250);
        });

        // Ẩn dropdown khi click ra ngoài
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.style.display = 'none';
            }
        });
    }

    // Khởi động tính user count nếu đang ở date_range
    const currentTarget = document.querySelector('input[name="target_type"]:checked');
    if (currentTarget && currentTarget.value === 'date_range') {
        fetchUserCount();
    }
});

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function addUserToSelection(id, name, email) {
    if (selectedUsers.some(u => u.id === id)) {
        return;
    }

    selectedUsers.push({ id, name, email });
    renderSelectedUsers();

    // Đánh dấu nút gợi ý đã chọn
    const suggestBtn = document.getElementById('suggestBtn_' + id);
    if (suggestBtn) suggestBtn.classList.add('added');

    // Xóa input và ẩn dropdown
    const searchInput = document.getElementById('userSearchInput');
    const resultsBox = document.getElementById('userSearchResults');
    if (searchInput) searchInput.value = '';
    if (resultsBox) resultsBox.style.display = 'none';
}

function removeUserFromSelection(id) {
    selectedUsers = selectedUsers.filter(u => u.id !== id);
    renderSelectedUsers();

    const suggestBtn = document.getElementById('suggestBtn_' + id);
    if (suggestBtn) suggestBtn.classList.remove('added');
}

function clearAllSelectedUsers() {
    selectedUsers = [];
    renderSelectedUsers();
    document.querySelectorAll('.suggestion-chip.added').forEach(btn => btn.classList.remove('added'));
}

function renderSelectedUsers() {
    const container = document.getElementById('selectedUsersContainer');
    const hiddenContainer = document.getElementById('hiddenInputsContainer');
    const clearAllBtn = document.getElementById('clearAllUsersBtn');

    if (!container || !hiddenContainer) return;

    if (selectedUsers.length === 0) {
        container.innerHTML = '<span style="font-size: 13px; color: var(--muted); font-style: italic;">Chưa có người dùng nào được chọn. Hãy tìm kiếm hoặc chọn từ danh sách gợi ý ở trên.</span>';
        hiddenContainer.innerHTML = '';
        if (clearAllBtn) clearAllBtn.style.display = 'none';
        return;
    }

    if (clearAllBtn) clearAllBtn.style.display = 'inline';

    container.innerHTML = selectedUsers.map(u => `
        <span class="user-chip">
            ${u.name} <small style="opacity:0.8;">(${u.email})</small>
            <span class="user-chip-remove" onclick="removeUserFromSelection(${u.id})">&times;</span>
        </span>
    `).join('');

    hiddenContainer.innerHTML = selectedUsers.map(u => `
        <input type="hidden" name="user_ids[]" value="${u.id}">
    `).join('');
}

function confirmSendNotification() {
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    let targetMsg = '';

    if (targetType === 'all') {
        targetMsg = 'TOÀN BỘ {{ $totalUsers }} người dùng';
    } else if (targetType === 'single') {
        if (selectedUsers.length === 0) {
            alert('Vui lòng chọn ít nhất 1 người dùng để gửi thông báo!');
            return false;
        }
        targetMsg = selectedUsers.length + ' người dùng cụ thể';
    } else {
        targetMsg = 'các người dùng theo mốc thời gian đăng ký đã chọn';
    }

    return confirm('Xác nhận gửi thông báo tới ' + targetMsg + '?\n\nHệ thống sẽ tiến hành gửi email và tạo thông báo trên web theo lựa chọn của bạn.');
}

function openLivePreviewFromForm() {
    const type = document.getElementById('type').value;
    const modal = document.getElementById('emailPreviewModal');
    const iframe = document.getElementById('previewIframe');
    if (!modal || !iframe) return;

    let url = '{{ route('admin.email.preview') }}?template=user_notification&type=' + encodeURIComponent(type);
    iframe.src = url;
    modal.style.display = 'flex';
}

function closePreviewModal() {
    const modal = document.getElementById('emailPreviewModal');
    const iframe = document.getElementById('previewIframe');
    if (modal) modal.style.display = 'none';
    if (iframe) iframe.src = 'about:blank';
}
</script>
@endsection
