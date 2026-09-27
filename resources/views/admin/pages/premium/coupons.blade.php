@extends('admin.layouts.master')

@section('content')
<section id="coupons" class="page">
    <div class="page-head">
        <div>
            <h3>Phiếu giảm giá</h3>
            <p>Tạo mã giảm giá, đặt điều kiện nhận và giới hạn lượt sử dụng.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="coupon-flash coupon-flash--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="coupon-flash coupon-flash--error">{{ $errors->first() }}</div>
    @endif

    <div class="panel coupon-create-panel">
        <div class="panel-head coupon-create-panel__head">
            <button type="button" class="coupon-create-toggle {{ $errors->any() ? 'is-open' : '' }}" aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-controls="couponCreateForm">
                <span><strong>Tạo phiếu giảm giá</strong><small>Bấm để {{ $errors->any() ? 'thu gọn' : 'mở biểu mẫu' }}</small></span>
                <span class="coupon-create-chevron" aria-hidden="true">⌄</span>
            </button>
        </div>
        <div class="panel-body coupon-create-body" id="couponCreateForm" @if(!$errors->any()) hidden @endif>
            <form action="{{ route('admin.premium.coupons.store') }}" method="POST" class="coupon-form">
                @csrf
                <div class="coupon-field">
                    <label for="coupon-code">Mã coupon</label>
                    <input id="coupon-code" type="text" name="code" value="{{ old('code') }}" placeholder="VD: WELCOME20" maxlength="50" required>
                    @error('code')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="coupon-title">Tiêu đề ưu đãi</label>
                    <input id="coupon-title" type="text" name="title" value="{{ old('title') }}" placeholder="VD: Giảm 20% gói Premium" maxlength="180" required>
                    @error('title')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field coupon-field--wide">
                    <label for="coupon-description">Mô tả hiển thị trên trang chủ</label>
                    <textarea id="coupon-description" name="description" rows="2" placeholder="Thông tin ngắn về chương trình">{{ old('description') }}</textarea>
                    @error('description')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="discount-type">Hình thức giảm</label>
                    <select id="discount-type" name="discount_type" required>
                        <option value="percentage" {{ old('discount_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Số tiền (₫)</option>
                    </select>
                </div>
                <div class="coupon-field">
                    <label for="discount-value">Mức giảm</label>
                    <input id="discount-value" type="number" name="discount_value" value="{{ old('discount_value') }}" min="1" max="100000000" required>
                    @error('discount_value')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="eligibility-type">Điều kiện nhận</label>
                    <select id="eligibility-type" name="eligibility_type" required>
                        <option value="all" {{ old('eligibility_type', 'all') === 'all' ? 'selected' : '' }}>Mọi tài khoản</option>
                        <option value="account_age_days" {{ old('eligibility_type') === 'account_age_days' ? 'selected' : '' }}>Tài khoản đủ số ngày</option>
                        <option value="watch_hours" {{ old('eligibility_type') === 'watch_hours' ? 'selected' : '' }}>Đã xem đủ số giờ</option>
                        <option value="premium_spend" {{ old('eligibility_type') === 'premium_spend' ? 'selected' : '' }}>Đã chi tiêu Premium đủ mức</option>
                    </select>
                </div>
                <div class="coupon-field" id="eligibility-value-field">
                    <label for="eligibility-value">Mốc điều kiện</label>
                    <input id="eligibility-value" type="number" name="eligibility_value" value="{{ old('eligibility_value') }}" min="1" max="100000000" placeholder="Ngày / giờ / đồng">
                    <small id="eligibility-hint">Không cần nhập nếu áp dụng cho mọi tài khoản.</small>
                    @error('eligibility_value')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="starts-at">Bắt đầu</label>
                    <input id="starts-at" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">
                    @error('starts_at')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="ends-at">Kết thúc</label>
                    <input id="ends-at" type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">
                    @error('ends_at')<small>{{ $message }}</small>@enderror
                </div>
                <div class="coupon-field">
                    <label for="usage-limit">Giới hạn lượt dùng</label>
                    <input id="usage-limit" type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Để trống = không giới hạn">
                    @error('usage_limit')<small>{{ $message }}</small>@enderror
                </div>
                <label class="coupon-active-field">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <span>Kích hoạt coupon</span>
                </label>
                <div class="coupon-field coupon-field--wide coupon-submit-row">
                    <button type="submit" class="btn">Tạo phiếu ưu đãi</button>
                    <p>User đủ điều kiện sẽ tự nhận phiếu khi truy cập trang chủ.</p>
                </div>
            </form>
        </div>
    </div>

    <div class="panel coupon-list-panel">
        <div class="panel-head"><h4>Danh sách coupon <span class="coupon-count">{{ $coupons->total() }}</span></h4></div>
        <div class="coupon-table-wrap">
            <table class="coupon-table">
                <thead>
                    <tr><th>Coupon</th><th>Mức giảm</th><th>Điều kiện nhận</th><th>Thời gian</th><th>Đã cấp</th><th>Đã dùng</th><th>Trạng thái</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        @php
                            $eligibilityLabel = match ($coupon->eligibility_type) {
                                'account_age_days' => 'Tài khoản từ ' . $coupon->eligibility_value . ' ngày',
                                'watch_hours' => 'Đã xem ' . $coupon->eligibility_value . ' giờ',
                                'premium_spend' => 'Chi tiêu từ ₫' . number_format($coupon->eligibility_value),
                                default => 'Mọi tài khoản',
                            };
                            $windowLabel = ($coupon->starts_at?->format('d/m/y') ?? 'Bắt đầu ngay') . ' – ' . ($coupon->ends_at?->format('d/m/y') ?? 'Không hết hạn');
                            $statusLabel = !$coupon->is_active
                                ? 'Đã tắt'
                                : ($coupon->starts_at?->isFuture()
                                    ? 'Chưa bắt đầu'
                                    : ($coupon->ends_at?->isPast()
                                        ? 'Hết hạn'
                                        : (!$coupon->isAvailableToday()
                                            ? 'Ngoài lịch hôm nay'
                                            : ($coupon->usage_limit && ($coupon->usage_count + $coupon->reserved_count) >= $coupon->usage_limit ? 'Hết lượt' : 'Đang bật'))));
                        @endphp
                        <tr>
                            <td><span class="coupon-code">{{ $coupon->code }}</span><strong>{{ $coupon->title }}</strong>@if($coupon->description)<small>{{ $coupon->description }}</small>@endif</td>
                            <td>{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₫' . number_format($coupon->discount_value) }}</td>
                            <td>{{ $eligibilityLabel }}</td>
                            <td>{{ $windowLabel }}</td>
                            <td>{{ number_format($coupon->user_grants_count) }}</td>
                            <td>{{ number_format($coupon->usage_count) }}{{ $coupon->usage_limit ? ' / ' . number_format($coupon->usage_limit) : '' }}{{ $coupon->reserved_count ? ' (' . $coupon->reserved_count . ' đang thanh toán)' : '' }}</td>
                            <td><span class="coupon-status {{ $coupon->isAvailable() ? 'is-active' : 'is-off' }}">{{ $statusLabel }}</span></td>
                            <td class="coupon-actions">
                                <form action="{{ route('admin.premium.coupons.toggle', $coupon) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="mini">{{ $coupon->is_active ? 'Tắt' : 'Bật' }}</button></form>
                                <form action="{{ route('admin.premium.coupons.delete', $coupon) }}" method="POST" onsubmit="return confirm('Xóa coupon {{ $coupon->code }}?')">@csrf @method('DELETE')<button type="submit" class="mini coupon-delete">Xóa</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="coupon-empty">Chưa có coupon. Tạo coupon đầu tiên ở biểu mẫu phía trên.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="coupon-pagination">{{ $coupons->links() }}</div>
    </div>
</section>

<style>
    .coupon-flash{padding:13px 16px;border-radius:10px;margin-bottom:16px}.coupon-flash--success{background:#064e3b;color:#a7f3d0}.coupon-flash--error{background:#7f1d1d;color:#fecaca}
    .coupon-create-panel,.coupon-list-panel{margin-bottom:24px}.coupon-create-panel .panel-head,.coupon-list-panel .panel-head{padding:18px 22px;border-bottom:1px solid var(--line,#2b303a)}.coupon-create-panel .panel-head h4,.coupon-list-panel .panel-head h4{margin:0}.coupon-create-panel__head{padding:0!important}.coupon-create-toggle{display:flex;width:100%;align-items:center;justify-content:space-between;gap:16px;padding:18px 22px;border:0;background:transparent;color:inherit;text-align:left;cursor:pointer}.coupon-create-toggle>span:first-child{display:grid;gap:4px}.coupon-create-toggle strong{font-size:15px}.coupon-create-toggle small{color:var(--muted,#9ca3af);font-size:12px}.coupon-create-chevron{color:#c4b5fd;font-size:23px;transition:transform .18s ease}.coupon-create-toggle.is-open .coupon-create-chevron{transform:rotate(180deg)}.coupon-create-body[hidden]{display:none!important}
    .coupon-form{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;padding:22px}.coupon-field{display:flex;flex-direction:column;gap:7px;min-width:0}.coupon-field label{font-size:13px;font-weight:700}.coupon-field input:not([type=checkbox]),.coupon-field select,.coupon-field textarea{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#111827}.coupon-field select option{background:#fff;color:#111827}.coupon-field small{color:#fca5a5;font-size:12px}.coupon-field small#eligibility-hint{color:var(--muted,#9ca3af)}.coupon-field--wide{grid-column:1/-1}.coupon-active-field{display:flex;align-items:center;gap:9px;align-self:center;font-size:13px}.coupon-active-field input{accent-color:#8b5cf6}.coupon-submit-row{display:flex;align-items:center;gap:14px}.coupon-submit-row p{margin:0;color:var(--muted,#9ca3af);font-size:12px}.coupon-count{margin-left:7px;padding:3px 8px;border-radius:999px;background:#27213c;color:#c4b5fd;font-size:12px}.coupon-weekdays{display:flex;flex-wrap:wrap;gap:8px}.coupon-weekdays label{display:inline-flex;align-items:center;gap:6px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#111827;cursor:pointer}.coupon-weekdays input{width:auto!important;accent-color:#7c3aed}.coupon-days-hint{color:var(--muted,#9ca3af)!important}
    .coupon-table-wrap{overflow-x:auto}.coupon-table{width:100%;border-collapse:collapse}.coupon-table th,.coupon-table td{padding:14px 16px;border-bottom:1px solid var(--line,#2b303a);text-align:left;vertical-align:middle}.coupon-table th{font-size:11px;color:var(--muted,#9ca3af);text-transform:uppercase;letter-spacing:.06em}.coupon-table td:first-child{min-width:200px}.coupon-table td:first-child strong,.coupon-table td:first-child small{display:block;margin-top:5px}.coupon-table td:first-child small{max-width:240px;color:var(--muted,#9ca3af);white-space:normal}.coupon-code{display:inline-block;padding:4px 7px;border-radius:5px;background:#29213f;color:#c4b5fd;font-size:12px;font-weight:800}.coupon-status{display:inline-block;padding:5px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap}.coupon-status.is-active{background:#064e3b;color:#a7f3d0}.coupon-status.is-off{background:#3f3f46;color:#d4d4d8}.coupon-actions{display:flex;gap:6px}.coupon-actions form{margin:0}.coupon-actions .mini{padding:6px 8px;cursor:pointer}.coupon-actions .coupon-delete{color:#fca5a5}.coupon-empty{text-align:center!important;color:var(--muted,#9ca3af);padding:32px!important}.coupon-pagination{padding:18px 22px}
    @media(max-width:850px){.coupon-form{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:580px){.coupon-form{grid-template-columns:1fr;padding:16px}.coupon-field--wide{grid-column:auto}.coupon-submit-row{align-items:flex-start;flex-direction:column}}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const createToggle=document.querySelector('.coupon-create-toggle');
    const createForm=document.getElementById('couponCreateForm');
    createToggle.addEventListener('click',function(){
        const isOpen=createToggle.getAttribute('aria-expanded')==='true';
        createToggle.setAttribute('aria-expanded',isOpen?'false':'true');
        createToggle.classList.toggle('is-open',!isOpen);
        createForm.hidden=isOpen;
        createToggle.querySelector('small').textContent=isOpen?'Bấm để mở biểu mẫu':'Bấm để thu gọn';
    });

    const type=document.getElementById('eligibility-type');
    const value=document.getElementById('eligibility-value');
    const hint=document.getElementById('eligibility-hint');
    const hints={account_age_days:'Số ngày kể từ khi tài khoản đăng ký.',watch_hours:'Tổng số giờ đã xem phim trên tài khoản.',premium_spend:'Tổng số tiền đã thanh toán gói Premium (₫).'};
    function updateEligibility(){const applies=type.value!=='all';value.required=applies;value.disabled=!applies;hint.textContent=hints[type.value]||'Không cần nhập nếu áp dụng cho mọi tài khoản.';}
    type.addEventListener('change',updateEligibility);updateEligibility();
});
</script>
@endsection
