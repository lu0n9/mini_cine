@extends('admin.layouts.master')

@section('content')
<section class="page promotion-admin">
    <div class="page-head">
        <div>
            <h3>Khuyến mại theo ngày</h3>
            <p>Tạo thông điệp khuyến mại độc lập với coupon và lên lịch hiển thị trên trang chủ.</p>
        </div>
    </div>

    @if (session('success'))<div class="promotion-flash promotion-flash--success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="promotion-flash promotion-flash--error">{{ $errors->first() }}</div>@endif

    <div class="panel promotion-panel">
        <div class="panel-head promotion-create-head">
            <button type="button" class="promotion-create-toggle {{ $errors->any() ? 'is-open' : '' }}" aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-controls="promotionCreateForm">
                <span><strong>Tạo khuyến mại</strong><small>Bấm để {{ $errors->any() ? 'thu gọn' : 'mở biểu mẫu' }}</small></span>
                <span class="promotion-create-chevron" aria-hidden="true">⌄</span>
            </button>
        </div>
        <div id="promotionCreateForm" class="promotion-create-body" @if(!$errors->any()) hidden @endif>
        <form action="{{ route('admin.premium.promotions.store') }}" method="POST" class="promotion-form">
            @csrf
            <div class="promotion-field">
                <label for="promotion-title">Tiêu đề</label>
                <input id="promotion-title" name="title" value="{{ old('title') }}" maxlength="180" placeholder="VD: Ưu đãi Thứ Sáu" required>
                @error('title')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field">
                <label for="promotion-url">Liên kết chi tiết (không bắt buộc)</label>
                <input id="promotion-url" type="url" name="action_url" value="{{ old('action_url') }}" placeholder="https://...">
                @error('action_url')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field">
                <label for="promotion-discount-type">Kiểu giảm giá</label>
                <select id="promotion-discount-type" name="discount_type" required>
                    <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Số tiền (₫)</option>
                </select>
                @error('discount_type')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field">
                <label for="promotion-discount-value">Mức giảm</label>
                <input id="promotion-discount-value" type="number" name="discount_value" value="{{ old('discount_value') }}" min="1" max="100000000" required placeholder="VD: 20">
                @error('discount_value')<small>{{ $message }}</small>@enderror
            </div>
            <label class="promotion-active">
                <input type="hidden" name="applies_to_upgrades" value="0">
                <input type="checkbox" name="applies_to_upgrades" value="1" {{ old('applies_to_upgrades') == '1' ? 'checked' : '' }}>
                <span>Áp dụng mức giảm này khi user nâng cấp gói</span>
            </label>
            <div class="promotion-field promotion-field--wide">
                <label>Gói Premium được giảm giá</label>
                <div class="promotion-plans">
                    @forelse($plans as $plan)
                        <label><input type="checkbox" name="plan_ids[]" value="{{ $plan->id }}" {{ in_array((string) $plan->id, array_map('strval', is_array(old('plan_ids', [])) ? old('plan_ids', []) : []), true) ? 'checked' : '' }}><span>{{ $plan->name }} · ₫{{ number_format($plan->price, 0, ',', '.') }}</span></label>
                    @empty
                        <small>Chưa có gói Premium đang hoạt động.</small>
                    @endforelse
                </div>
                @error('plan_ids')<small>{{ $message }}</small>@enderror
                @error('plan_ids.*')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field promotion-field--wide">
                <label for="promotion-message">Nội dung khuyến mại</label>
                <textarea id="promotion-message" name="message" rows="3" maxlength="2000" placeholder="Nhập nội dung sẽ chạy trên thanh khuyến mại trang chủ" required>{{ old('message') }}</textarea>
                @error('message')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field">
                <label for="promotion-start">Bắt đầu</label>
                <input id="promotion-start" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">
                @error('starts_at')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field">
                <label for="promotion-end">Kết thúc</label>
                <input id="promotion-end" type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">
                @error('ends_at')<small>{{ $message }}</small>@enderror
            </div>
            <div class="promotion-field promotion-field--wide">
                <label>Ngày chạy trong tuần</label>
                <div class="promotion-weekdays">
                    @foreach ([1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'] as $day => $label)
                        <label><input type="checkbox" name="available_days[]" value="{{ $day }}" {{ in_array((string) $day, array_map('strval', is_array(old('available_days', [])) ? old('available_days', []) : []), true) ? 'checked' : '' }}><span>{{ $label }}</span></label>
                    @endforeach
                </div>
                <small>Chọn một hoặc nhiều ngày. Để trống nếu khuyến mại chạy mỗi ngày trong khoảng thời gian đã đặt.</small>
                @error('available_days.*')<small>{{ $message }}</small>@enderror
            </div>
            <label class="promotion-active">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <span>Kích hoạt khuyến mại</span>
            </label>
            <div class="promotion-field promotion-field--wide promotion-submit">
                <button type="submit" class="btn">Tạo khuyến mại</button>
                <span>Khuyến mại giảm trực tiếp giá các gói đã chọn và chạy giữa menu với phim đề xuất cho user đã đăng nhập.</span>
            </div>
        </form>
        </div>
    </div>

    <div class="panel promotion-panel">
        <div class="panel-head"><h4>Danh sách khuyến mại <span class="promotion-total">{{ $promotions->total() }}</span></h4></div>
        <div class="promotion-table-wrap">
            <table class="promotion-table">
                <thead><tr><th>Tiêu đề / nội dung</th><th>Mức giảm / gói</th><th>Lịch ngày</th><th>Thời hạn</th><th>Trạng thái</th><th></th></tr></thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        @php
                            $dayLabels = [1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 7 => 'CN'];
                            $daysText = empty($promotion->available_days)
                                ? 'Mỗi ngày'
                                : collect($promotion->available_days)->map(fn ($day) => $dayLabels[(int) $day] ?? '')->filter()->join(', ');
                            $activeNow = $promotion->isAvailableNow();
                            $statusText = !$promotion->is_active
                                ? 'Đã tắt'
                                : ($promotion->starts_at?->isFuture()
                                    ? 'Chưa bắt đầu'
                                    : ($promotion->ends_at?->isPast()
                                        ? 'Hết hạn'
                                        : (!$promotion->isAvailableNow() ? 'Ngoài lịch hôm nay' : 'Đang chạy')));
                        @endphp
                        <tr>
                            <td><strong>{{ $promotion->title }}</strong><span>{{ $promotion->message }}</span>@if($promotion->action_url)<a href="{{ $promotion->action_url }}" target="_blank" rel="noopener noreferrer">Mở liên kết ↗</a>@endif</td>
                            <td>{{ $promotion->discount_type === 'percentage' ? $promotion->discount_value . '%' : '₫' . number_format($promotion->discount_value, 0, ',', '.') }}<br><small>{{ $promotion->plans->pluck('name')->join(', ') ?: 'Chưa chọn gói' }}</small>@if($promotion->applies_to_upgrades)<br><small>Áp dụng khi nâng cấp</small>@endif</td>
                            <td>{{ $daysText }}</td>
                            <td>{{ $promotion->starts_at?->format('d/m/Y H:i') ?? 'Ngay lập tức' }}<br>đến {{ $promotion->ends_at?->format('d/m/Y H:i') ?? 'Không giới hạn' }}</td>
                            <td><span class="promotion-status {{ $activeNow ? 'is-running' : 'is-off' }}">{{ $statusText }}</span></td>
                            <td class="promotion-actions">
                                <form action="{{ route('admin.premium.promotions.toggle', $promotion) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="mini">{{ $promotion->is_active ? 'Tắt' : 'Bật' }}</button></form>
                                <form action="{{ route('admin.premium.promotions.delete', $promotion) }}" method="POST" onsubmit="return confirm('Xóa khuyến mại này?')">@csrf @method('DELETE')<button type="submit" class="mini">Xóa</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="promotion-empty">Chưa có khuyến mại nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="promotion-pagination">{{ $promotions->links() }}</div>
    </div>
</section>

<style>
    .promotion-flash{padding:13px 16px;border-radius:10px;margin-bottom:16px}.promotion-flash--success{background:#064e3b;color:#a7f3d0}.promotion-flash--error{background:#7f1d1d;color:#fecaca}.promotion-panel{margin-bottom:24px}.promotion-panel>.panel-head{padding:18px 22px;border-bottom:1px solid var(--line,#2b303a)}.promotion-create-head{padding:0!important}.promotion-create-toggle{display:flex;width:100%;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px;border:0;background:transparent;color:inherit;text-align:left;cursor:pointer}.promotion-create-toggle>span:first-child{display:grid;gap:4px}.promotion-create-toggle strong{font-size:15px}.promotion-create-toggle small{color:var(--muted,#9ca3af);font-size:12px}.promotion-create-chevron{color:#c4b5fd;font-size:23px;transition:transform .18s ease}.promotion-create-toggle.is-open .promotion-create-chevron{transform:rotate(180deg)}.promotion-create-body[hidden]{display:none!important}.promotion-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;padding:22px}.promotion-field{display:flex;flex-direction:column;gap:7px;min-width:0}.promotion-field label{font-size:13px;font-weight:700}.promotion-field input:not([type=checkbox]),.promotion-field textarea,.promotion-field select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#111827}.promotion-field--wide{grid-column:1/-1}.promotion-field small{color:#fca5a5;font-size:12px}.promotion-field--wide>small{color:var(--muted,#9ca3af)}.promotion-weekdays,.promotion-plans{display:flex;flex-wrap:wrap;gap:8px}.promotion-weekdays label,.promotion-plans label{display:inline-flex;align-items:center;gap:6px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#111827;cursor:pointer}.promotion-weekdays input,.promotion-plans input,.promotion-active input{accent-color:#7c3aed}.promotion-active{display:flex;align-items:center;gap:8px;font-size:13px}.promotion-submit{display:flex;align-items:center;gap:14px}.promotion-submit span{color:var(--muted,#9ca3af);font-size:12px}.promotion-table-wrap{overflow-x:auto}.promotion-table{width:100%;border-collapse:collapse}.promotion-table th,.promotion-table td{padding:14px 16px;border-bottom:1px solid var(--line,#2b303a);text-align:left;vertical-align:top}.promotion-table th{font-size:11px;color:var(--muted,#9ca3af);text-transform:uppercase;letter-spacing:.06em}.promotion-table td:first-child{min-width:260px}.promotion-table td:first-child strong,.promotion-table td:first-child span,.promotion-table td:first-child a{display:block;margin-bottom:6px}.promotion-table td:first-child span{max-width:440px;color:var(--muted,#9ca3af);white-space:pre-line}.promotion-table td:first-child a{font-size:12px;color:#c4b5fd}.promotion-table td small{color:var(--muted,#9ca3af)}.promotion-status{display:inline-block;padding:5px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap}.promotion-status.is-running{background:#064e3b;color:#a7f3d0}.promotion-status.is-off{background:#3f3f46;color:#d4d4d8}.promotion-actions{display:flex;gap:6px}.promotion-actions form{margin:0}.promotion-actions .mini{padding:6px 8px;cursor:pointer}.promotion-empty{text-align:center!important;color:var(--muted,#9ca3af);padding:32px!important}.promotion-total{margin-left:7px;padding:3px 8px;border-radius:999px;background:#27213c;color:#c4b5fd;font-size:12px}.promotion-pagination{padding:18px 22px}@media(max-width:650px){.promotion-form{grid-template-columns:1fr;padding:16px}.promotion-field--wide{grid-column:auto}.promotion-submit{align-items:flex-start;flex-direction:column}}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const toggle=document.querySelector('.promotion-create-toggle');
    const form=document.getElementById('promotionCreateForm');
    toggle.addEventListener('click',function(){
        const isOpen=toggle.getAttribute('aria-expanded')==='true';
        toggle.setAttribute('aria-expanded',isOpen?'false':'true');
        toggle.classList.toggle('is-open',!isOpen);
        form.hidden=isOpen;
        toggle.querySelector('small').textContent=isOpen?'Bấm để mở biểu mẫu':'Bấm để thu gọn';
    });
});
</script>
@endsection
