@extends('admin.layouts.master')

@section('content')
<section id="api" class="page">
    <div class="page-head">
        <div>
            <h3>API Management</h3>
            <p>Quản lý kết nối Gemini, Supabase và cấu hình cổng thanh toán dùng trong hệ thống.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form action="{{ route('admin.system.api.sync') }}" method="POST" style="display: inline-flex; margin: 0;">
                @csrf
                <button type="submit" class="btn ghost" title="Đồng bộ các API thực tế từ dự án & .env">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    Nhập cấu hình .env
                </button>
            </form>
            <button type="button" class="btn" onclick="openCreateApiKeyModal()">
                + Tạo API key
            </button>
        </div>
    </div>

    {{-- THÔNG BÁO FLASH --}}
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
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul style="margin: 6px 0 0 18px; font-size: 13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CẤU HÌNH CỔNG THANH TOÁN --}}
    <div class="panel" style="margin-bottom: 24px; border: 1px solid rgba(124, 58, 237, .35);">
        <div class="panel-head" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
            <div>
                <h4 style="margin:0">Cấu hình thanh toán · VNPay</h4>
                <p style="font-size:12.5px;color:var(--muted);margin:5px 0 0">Thông tin này được dùng khi người dùng mua gói Premium hoặc Premium Extra.</p>
            </div>
            @if($vnpayEnabled && $vnpayConfigured)
                <span class="status">Đang bật</span>
            @elseif($vnpayConfigured)
                <span class="status off">Đã cấu hình · Đang tắt</span>
            @else
                <span class="status off">Chưa cấu hình</span>
            @endif
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.system.api.payment-config') }}" method="POST">
                @csrf
                <div class="form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                    <div class="field">
                        <label for="vnpay_merchant_code" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Mã website / Merchant</label>
                        <input id="vnpay_merchant_code" name="merchant_code" value="{{ old('merchant_code', $vnpayMerchantCode) }}" required maxlength="100" autocomplete="off" placeholder="Mã do VNPay cấp" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--line);background:var(--panel-2);color:var(--fg)">
                    </div>
                    <div class="field">
                        <label for="vnpay_secret_key" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Secret Key</label>
                        <input id="vnpay_secret_key" type="password" name="secret_key" value="" maxlength="500" autocomplete="new-password" placeholder="{{ $vnpayConfigured ? 'Đã lưu · để trống để giữ nguyên' : 'Nhập Secret Key VNPay' }}" {{ $vnpayConfigured ? '' : 'required' }} style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--line);background:var(--panel-2);color:var(--fg)">
                        <small style="display:block;color:var(--muted);margin-top:5px">Secret được mã hóa trong database và không hiển thị lại tại đây.</small>
                    </div>
                    <div class="field">
                        <label for="vnpay_payment_url" style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Môi trường thanh toán</label>
                        <select id="vnpay_payment_url" name="payment_url" required style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--line);background:var(--panel-2);color:var(--fg)">
                            <option value="https://sandbox.vnpayment.vn/paymentv2/vpcpay.html" @selected(old('payment_url', $vnpayUrl) === 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html')>Sandbox · kiểm thử</option>
                            <option value="https://pay.vnpay.vn/vpcpay.html" @selected(old('payment_url', $vnpayUrl) === 'https://pay.vnpay.vn/vpcpay.html')>Production · thanh toán thật</option>
                        </select>
                    </div>
                </div>
                <label style="display:flex;align-items:center;gap:8px;margin:16px 0;font-size:13px">
                    <input type="checkbox" name="is_enabled" value="1" @checked((bool) old('is_enabled', $vnpayEnabled))>
                    Cho phép thanh toán qua VNPay
                </label>
                <button type="submit" class="btn">Lưu cấu hình VNPay</button>
            </form>
        </div>
    </div>

    {{-- BANNER SAO CHÉP KEY VỪA TẠO --}}
    @if(session('new_api_key'))
        <div class="panel" style="margin-bottom: 24px; border: 1px solid rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.05);">
            <div class="panel-body">
                <div>
                    <strong style="color: #10b981; font-size: 15px; display: block; margin-bottom: 4px;">
                        Khóa API mới cho "{{ session('new_api_name') }}" đã được tạo thành công!
                    </strong>
                    <p style="font-size: 13px; color: var(--muted); margin: 0;">
                        Hãy sao chép và lưu khóa này vào hệ thống của bạn ngay bây giờ. Vì lý do bảo mật, toàn bộ khóa sẽ được che mờ sau khi rời khỏi trang.
                    </p>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 14px; align-items: center; max-width: 640px;">
                    <input type="text" id="createdApiKeyInput" value="{{ session('new_api_key') }}" readonly
                           style="flex: 1; padding: 10px 14px; font-family: monospace; font-size: 13.5px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); background: var(--bg); color: var(--fg); outline: none;">
                    <button type="button" class="btn" onclick="copyCreatedApiKey()" id="copyKeyBtn" style="white-space: nowrap;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        Sao chép
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="panel" style="margin-bottom: 16px;">
        <div class="panel-body" style="padding: 12px 16px;">
            <form method="GET" action="{{ route('admin.system.api') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <select name="type" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none; min-width: 160px;">
                    <option value="">Tất cả loại API</option>
                    @foreach($apiTypeLabels as $typeValue => $typeLabel)
                        <option value="{{ $typeValue }}" {{ request('type') == $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                    @endforeach
                </select>
                <select name="status" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none; min-width: 130px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked</option>
                </select>
                <input type="text" name="search" placeholder="Tìm kiếm theo tên..." value="{{ request('search') }}" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none; flex: 1; min-width: 180px;">
                <button type="submit" class="btn ghost" style="padding: 8px 16px;">Lọc</button>
                @if(request('type') || request('status') || request('search'))
                    <a href="{{ route('admin.system.api') }}" class="btn ghost" style="padding: 8px 16px; text-decoration: none;">Xóa lọc</a>
                @endif
            </form>
        </div>
    </div>

    {{-- BẢNG DANH SÁCH API VỚI BULK TOGGLE --}}
    <form id="bulkForm" method="POST" action="{{ route('admin.system.api.bulk-toggle') }}">
        @csrf
    </form>
        <div class="panel">
            {{-- Bulk action bar --}}
            <div id="bulkActionBar" style="display: none; padding: 10px 16px; background: rgba(59, 130, 246, 0.08); border-bottom: 1px solid rgba(59, 130, 246, 0.15); align-items: center; gap: 12px;">
                <span style="font-size: 13px; color: var(--fg);"><strong id="selectedCount">0</strong> API đã chọn</span>
                <button type="submit" form="bulkForm" class="btn ghost" style="padding: 6px 14px; font-size: 12.5px;">
                    ⇄ Toggle trạng thái đã chọn
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 36px; text-align: center;">
                            <input type="checkbox" id="selectAll" title="Chọn tất cả" style="cursor: pointer;">
                        </th>
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Key</th>
                        <th>Endpoint</th>
                        <th>Rate limit</th>
                        <th>Lượt gọi</th>
                        <th>Trạng thái</th>
                        <th style="text-align: right; width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiKeys as $key)
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" form="bulkForm" name="selected[]" value="{{ $key->id }}" class="row-checkbox" style="cursor: pointer;">
                            </td>
                            <td>
                                <strong>{{ $key->name }}</strong>
                                @if($key->expires_at)
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                        Hạn: {{ $key->expires_at->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 500;
                                    @switch($key->type)
                                        @case('video_storage') background: rgba(59, 130, 246, 0.12); color: #3b82f6; @break
                                        @case('ai_moderation') background: rgba(168, 85, 247, 0.12); color: #a855f7; @break
                                        @case('movie_metadata') background: rgba(245, 158, 11, 0.12); color: #f59e0b; @break
                                        @case('client_api') background: rgba(16, 185, 129, 0.12); color: #10b981; @break
                                        @case('webhook') background: rgba(107, 114, 128, 0.12); color: #6b7280; @break
                                        @default background: rgba(255,255,255,0.08); color: var(--muted);
                                    @endswitch
                                ">
                                    {{ $apiTypeLabels[$key->type] ?? $key->type }}
                                </span>
                            </td>
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <code style="background: rgba(255,255,255,0.06); padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 12.5px; letter-spacing: 0.5px;">{{ $key->masked_key }}</code>
                                </div>
                            </td>
                            <td>
                                @if($key->endpoint)
                                    <code style="font-size: 11.5px; opacity: 0.8; word-break: break-all;">{{ Str::limit($key->endpoint, 35) }}</code>
                                @else
                                    <span style="color: var(--muted); font-size: 12px;">—</span>
                                @endif
                            </td>
                            <td>
                                {{ number_format($key->rate_limit) }}/phút
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ $key->formatted_usage }}</span>
                            </td>
                            <td>
                                @if($key->status === 'active')
                                    <span class="status">Active</span>
                                @else
                                    <span class="status off">Revoked</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions" style="justify-content: flex-end; gap: 6px;">
                                    {{-- Toggle nhanh --}}
                                    @if($key->status === 'active')
                                        <form action="{{ route('admin.system.api.revoke', $key->id) }}" method="POST" style="display:inline;" title="Tắt API">
                                            @csrf
                                            <button type="submit" class="mini" style="font-size: 16px; color: #10b981; border: none; background: transparent; cursor: pointer;" title="Tắt">⏻</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.system.api.activate', $key->id) }}" method="POST" style="display:inline;" title="Bật API">
                                            @csrf
                                            <button type="submit" class="mini" style="font-size: 16px; color: #ef4444; border: none; background: transparent; cursor: pointer;" title="Bật">⏻</button>
                                        </form>
                                    @endif

                                    {{-- Nút sửa API --}}
                                    <button type="button" class="mini" data-api-id="{{ $key->id }}" data-name="{{ $key->name }}" data-type="{{ $key->type }}" data-endpoint="{{ $key->endpoint }}" data-rate-limit="{{ $key->rate_limit }}" data-status="{{ $key->status }}" data-expires-at="{{ $key->expires_at?->format('Y-m-d\TH:i') }}" title="Chỉnh sửa API" onclick="openEditApiKeyModal(this)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>

                                    {{-- Nút xóa vĩnh viễn --}}
                                    <form action="{{ route('admin.system.api.delete', $key->id) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn API &quot;{{ $key->name }}&quot; khỏi hệ thống?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mini" title="Xóa vĩnh viễn" style="color: #ef4444;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px; color: var(--muted); font-size: 14px;">
                                Chưa có API nào trong hệ thống. Bấm <strong>"Nhập cấu hình .env"</strong> hoặc <strong>"+ Tạo API key"</strong> để thêm mới.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    {{-- PAGINATION --}}
    @if($apiKeys->hasPages())
        <div style="margin-top: 16px; display: flex; justify-content: center;">
            {{ $apiKeys->links() }}
        </div>
    @endif

    {{-- MODAL 1: TẠO API KEY MỚI --}}
    <div id="createApiKeyModal" class="api-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div class="panel" style="width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.15); max-height: 90vh; overflow-y: auto;">
            <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                <h4>+ Thêm API Mới</h4>
                <button type="button" onclick="closeCreateApiKeyModal()" class="mini" style="border: none; background: transparent; font-size: 18px; color: var(--muted); cursor: pointer;">✕</button>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.system.api.store') }}" method="POST" id="createApiKeyForm">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div class="field">
                            <label for="create_name" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Tên nhận diện <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="name" id="create_name" value="{{ old('_form') === 'create' ? old('name') : '' }}" placeholder="VD: Supabase Storage, Gemini AI..." required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        </div>
                        <div class="field">
                            <label for="create_type" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Loại API <span style="color: #ef4444;">*</span></label>
                            <select name="type" id="create_type" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                                @foreach($apiTypeLabels as $typeValue => $typeLabel)
                                    <option value="{{ $typeValue }}" {{ (old('_form') === 'create' ? old('type', 'custom') : 'custom') === $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 16px;">
                        <label for="create_endpoint" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Endpoint URL</label>
                        <input type="url" name="endpoint" id="create_endpoint" value="{{ old('_form') === 'create' ? old('endpoint') : '' }}" placeholder="https://example.supabase.co" style="width: 100%; padding: 10px 12px; font-family: monospace; font-size: 13px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        <small style="display: block; margin-top: 4px; color: var(--muted); font-size: 11.5px;">URL gốc của dịch vụ API. Bắt buộc cho Video Storage.</small>
                    </div>

                    <div class="field" style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label for="create_key" style="font-size: 13px; font-weight: 600; margin: 0;">Mã API Key / Token</label>
                            <button type="button" onclick="generateRandomCreateKey()" style="background: none; border: none; color: #10b981; font-size: 12px; cursor: pointer; text-decoration: underline; padding: 0;">Tự tạo ngẫu nhiên</button>
                        </div>
                        <input type="password" name="key" id="create_key" autocomplete="new-password" placeholder="Nhập key thật hoặc bấm 'Tự tạo ngẫu nhiên'" style="width: 100%; padding: 10px 12px; font-family: monospace; font-size: 13px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        <small style="display: block; margin-top: 4px; color: var(--muted); font-size: 11.5px;">Để trống nếu muốn hệ thống tự động sinh khóa ngẫu nhiên dạng sk_live_...</small>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div class="field">
                            <label for="create_rate_limit" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Hạn mức tham chiếu (/phút) <span style="color: #ef4444;">*</span></label>
                            <select name="rate_limit" id="create_rate_limit" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                                @foreach([60, 120, 300, 500, 1000, 5000] as $limit)
                                    <option value="{{ $limit }}" {{ (int) (old('_form') === 'create' ? old('rate_limit', 1000) : 1000) === $limit ? 'selected' : '' }}>{{ number_format($limit) }} req / phút</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="create_status" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Trạng thái</label>
                            <select name="status" id="create_status" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                                <option value="active" {{ (old('_form') === 'create' ? old('status', 'active') : 'active') === 'active' ? 'selected' : '' }}>Active (Kích hoạt)</option>
                                <option value="revoked" {{ old('_form') === 'create' && old('status') === 'revoked' ? 'selected' : '' }}>Revoked (Tạm khóa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 22px;">
                        <label for="create_expiry_preset" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Thời hạn hiệu lực</label>
                        <select name="expiry_preset" id="create_expiry_preset" onchange="toggleCustomExpiry('create')" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                            <option value="never" {{ (old('_form') === 'create' ? old('expiry_preset', 'never') : 'never') === 'never' ? 'selected' : '' }}>Không bao giờ hết hạn (Khuyên dùng)</option>
                            <option value="30_days" {{ old('_form') === 'create' && old('expiry_preset') === '30_days' ? 'selected' : '' }}>30 ngày</option>
                            <option value="90_days" {{ old('_form') === 'create' && old('expiry_preset') === '90_days' ? 'selected' : '' }}>90 ngày</option>
                            <option value="1_year" {{ old('_form') === 'create' && old('expiry_preset') === '1_year' ? 'selected' : '' }}>1 năm</option>
                            <option value="custom" {{ old('_form') === 'create' && old('expiry_preset') === 'custom' ? 'selected' : '' }}>Chọn ngày cụ thể...</option>
                        </select>
                        <div id="create_custom_date_wrap" style="display: {{ old('_form') === 'create' && old('expiry_preset') === 'custom' ? 'block' : 'none' }}; margin-top: 8px;">
                            <input type="date" name="custom_expires_at" id="create_custom_expires_at" value="{{ old('_form') === 'create' ? old('custom_expires_at') : '' }}" min="{{ now()->addDay()->toDateString() }}" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        </div>
                    </div>

                    <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn ghost" onclick="closeCreateApiKeyModal()">Hủy</button>
                        <button type="submit" class="btn">Tạo API</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: CHỈNH SỬA API --}}
    <div id="editApiKeyModal" class="api-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div class="panel" style="width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.15); max-height: 90vh; overflow-y: auto;">
            <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                <h4>Chỉnh sửa API</h4>
                <button type="button" onclick="closeEditApiKeyModal()" class="mini" style="border: none; background: transparent; font-size: 18px; color: var(--muted); cursor: pointer;">✕</button>
            </div>
            <div class="panel-body">
                <form action="" method="POST" id="editApiKeyForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="api_id" id="edit_api_id">

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div class="field">
                            <label for="edit_name" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Tên nhận diện <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        </div>
                        <div class="field">
                            <label for="edit_type" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Loại API <span style="color: #ef4444;">*</span></label>
                            <select name="type" id="edit_type" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                                @foreach($apiTypeLabels as $typeValue => $typeLabel)
                                    <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 16px;">
                        <label for="edit_endpoint" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Endpoint URL</label>
                        <input type="url" name="endpoint" id="edit_endpoint" placeholder="https://example.supabase.co" style="width: 100%; padding: 10px 12px; font-family: monospace; font-size: 13px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                    </div>

                    <div class="field" style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label for="edit_key" style="font-size: 13px; font-weight: 600; margin: 0;">Mã API Key / Token</label>
                            <button type="button" onclick="generateRandomEditKey()" style="background: none; border: none; color: #10b981; font-size: 12px; cursor: pointer; text-decoration: underline; padding: 0;">Đổi mã ngẫu nhiên</button>
                        </div>
                        <input type="password" name="key" id="edit_key" autocomplete="new-password" placeholder="Để trống để giữ nguyên key hiện tại" style="width: 100%; padding: 10px 12px; font-family: monospace; font-size: 13px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        <small style="display: block; margin-top: 4px; color: var(--muted); font-size: 11.5px;">Key hiện tại không hiển thị lại. Chỉ nhập khi cần thay khóa.</small>
                    </div>

                    <div class="field" style="margin-bottom: 16px;">
                        <label for="edit_expires_at" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Ngày hết hạn</label>
                        <input type="datetime-local" name="expires_at" id="edit_expires_at" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        <small style="display: block; margin-top: 4px; color: var(--muted); font-size: 11.5px;">Để trống nếu API không có ngày hết hạn.</small>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div class="field">
                            <label for="edit_rate_limit" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Hạn mức tham chiếu (/phút) <span style="color: #ef4444;">*</span></label>
                            <input type="number" name="rate_limit" id="edit_rate_limit" min="1" max="100000" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                        </div>
                        <div class="field">
                            <label for="edit_status" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Trạng thái <span style="color: #ef4444;">*</span></label>
                            <select name="status" id="edit_status" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: var(--panel-2); color: var(--fg); outline: none;">
                                <option value="active">Active (Kích hoạt)</option>
                                <option value="revoked">Revoked (Tạm khóa)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" class="btn ghost" onclick="closeEditApiKeyModal()">Hủy</button>
                        <button type="submit" class="btn">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Random key generator
    function makeRandomKey(prefix = 'sk_live_') {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let res = prefix;
        for (let i = 0; i < 32; i++) {
            res += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return res;
    }

    function generateRandomCreateKey() {
        document.getElementById('create_key').value = makeRandomKey();
    }

    function generateRandomEditKey() {
        document.getElementById('edit_key').value = makeRandomKey();
    }

    function toggleCustomExpiry(prefix) {
        const select = document.getElementById(prefix + '_expiry_preset');
        const wrap = document.getElementById(prefix + '_custom_date_wrap');
        if (select && wrap) {
            wrap.style.display = select.value === 'custom' ? 'block' : 'none';
        }
    }

    // Modal Create
    function openCreateApiKeyModal() {
        const modal = document.getElementById('createApiKeyModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                const input = document.getElementById('create_name');
                if (input) input.focus();
            }, 100);
        }
    }

    function closeCreateApiKeyModal() {
        const modal = document.getElementById('createApiKeyModal');
        if (modal) modal.style.display = 'none';
    }

    // Modal Edit
    function openEditApiKeyModal(button) {
        const modal = document.getElementById('editApiKeyModal');
        const form = document.getElementById('editApiKeyForm');
        if (!modal || !form || !button) return;

        const keyData = {
            id: button.dataset.apiId,
            name: button.dataset.name,
            type: button.dataset.type,
            endpoint: button.dataset.endpoint,
            rateLimit: button.dataset.rateLimit,
            status: button.dataset.status,
            expiresAt: button.dataset.expiresAt,
        };

        form.action = "{{ url('admin/system/api') }}/" + keyData.id;
        document.getElementById('edit_api_id').value = keyData.id;
        document.getElementById('edit_name').value = keyData.name || '';
        document.getElementById('edit_type').value = keyData.type || 'custom';
        document.getElementById('edit_endpoint').value = keyData.endpoint || '';
        document.getElementById('edit_key').value = '';
        document.getElementById('edit_rate_limit').value = keyData.rateLimit || 1000;
        document.getElementById('edit_status').value = keyData.status || 'active';
        document.getElementById('edit_expires_at').value = keyData.expiresAt || '';

        modal.style.display = 'flex';
        setTimeout(() => {
            const input = document.getElementById('edit_name');
            if (input) input.focus();
        }, 100);
    }

    function closeEditApiKeyModal() {
        const modal = document.getElementById('editApiKeyModal');
        if (modal) modal.style.display = 'none';
    }

    function copyCreatedApiKey() {
        const input = document.getElementById('createdApiKeyInput');
        const btn = document.getElementById('copyKeyBtn');
        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '✓ Đã sao chép!';
                setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
            }
        }).catch(() => {
            document.execCommand('copy');
            if (btn) {
                btn.innerHTML = '✓ Đã sao chép!';
                setTimeout(() => { btn.innerHTML = 'Sao chép'; }, 2000);
            }
        });
    }

    // Select all / bulk toggle logic
    document.addEventListener('DOMContentLoaded', function () {
        if (@json($errors->any() && old('_form') === 'create')) openCreateApiKeyModal();
        if (@json($errors->any() && old('_form') === 'edit')) {
            const id = @json(old('api_id'));
            const editButton = document.querySelector('[data-api-id="' + id + '"]');
            if (editButton) {
                editButton.click();
                document.getElementById('edit_name').value = @json(old('name'));
                document.getElementById('edit_type').value = @json(old('type'));
                document.getElementById('edit_endpoint').value = @json(old('endpoint'));
                document.getElementById('edit_rate_limit').value = @json(old('rate_limit'));
                document.getElementById('edit_status').value = @json(old('status'));
                document.getElementById('edit_expires_at').value = @json(old('expires_at'));
            }
        }

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkBar = document.getElementById('bulkActionBar');
        const countSpan = document.getElementById('selectedCount');

        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (bulkBar) {
                bulkBar.style.display = checked.length > 0 ? 'flex' : 'none';
            }
            if (countSpan) {
                countSpan.textContent = checked.length;
            }
            if (selectAll) {
                selectAll.checked = checked.length === checkboxes.length && checkboxes.length > 0;
                selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkBar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Close modals on Escape or outside click
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCreateApiKeyModal();
                closeEditApiKeyModal();
            }
        });

        const createModal = document.getElementById('createApiKeyModal');
        if (createModal) {
            createModal.addEventListener('click', function (e) {
                if (e.target === createModal) closeCreateApiKeyModal();
            });
        }

        const editModal = document.getElementById('editApiKeyModal');
        if (editModal) {
            editModal.addEventListener('click', function (e) {
                if (e.target === editModal) closeEditApiKeyModal();
            });
        }
    });
</script>
@endpush
@endsection
