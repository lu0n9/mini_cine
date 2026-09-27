@extends('client.layouts.master')
@section('title', 'Hóa đơn Premium')
@section('content')
<main class="premium-invoice-wrap">
    <article class="premium-invoice">
        <header class="premium-invoice__header">
            <div>
                <p class="premium-invoice__eyebrow">MINI CINE PREMIUM</p>
                <h1>Hóa đơn thanh toán</h1>
                <p class="premium-invoice__lead">Giao dịch Premium của bạn đã thanh toán thành công.</p>
            </div>
            <span class="premium-invoice__status">Đã thanh toán</span>
        </header>

        @if(session('success'))
            <div class="premium-invoice__notice">{{ session('success') }}</div>
        @endif

        <dl class="premium-invoice__meta">
            <div><dt>Mã hóa đơn</dt><dd>{{ $transaction->order_reference }}</dd></div>
            <div><dt>Ngày thanh toán</dt><dd>{{ $transaction->paid_at?->format('d/m/Y H:i') ?? $transaction->created_at?->format('d/m/Y H:i') }}</dd></div>
            <div><dt>Khách hàng</dt><dd>{{ $transaction->subscription->user?->name }} · {{ $transaction->subscription->user?->email }}</dd></div>
            <div><dt>Phương thức</dt><dd>{{ $transaction->provider === 'vnpay' ? 'VNPay' : 'Ưu đãi Premium' }}</dd></div>
            <div><dt>Loại giao dịch</dt><dd>{{ match($transaction->operation_type ?? 'purchase') { 'renewal' => 'Gia hạn', 'upgrade' => 'Nâng cấp', default => 'Mua mới' } }}</dd></div>
        </dl>

        <div class="premium-invoice__table-wrap">
            <table class="premium-invoice__table">
                <thead><tr><th>Chi tiết</th><th>Thành tiền</th></tr></thead>
                <tbody>
                    <tr>
                        <td>{{ $transaction->subscription->plan?->name ?? 'Gói Premium' }}
                            ({{ $transaction->subscription->plan?->billing_period === 'yearly' ? '12 tháng' : '1 tháng' }})</td>
                        <td>₫{{ number_format($transaction->base_amount ?? $transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    @if(($transaction->discount_amount ?? 0) > 0)
                        <tr class="premium-invoice__discount">
                            <td>{{ $transaction->discount_description ?: 'Ưu đãi Premium' }}</td>
                            <td>−₫{{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if(($transaction->upgrade_credit ?? 0) > 0)
                        <tr class="premium-invoice__discount">
                            <td>Giá trị còn lại của gói cũ được khấu trừ</td>
                            <td>−₫{{ number_format($transaction->upgrade_credit, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr><th>Đã thanh toán</th><th>₫{{ number_format($transaction->amount, 0, ',', '.') }}</th></tr>
                </tfoot>
            </table>
        </div>

        @if($transaction->provider_transaction_id)
            <p class="premium-invoice__provider">Mã giao dịch VNPay: <strong>{{ $transaction->provider_transaction_id }}</strong></p>
        @endif

        <footer class="premium-invoice__actions">
            <a href="{{ route('premium.index') }}">Quản lý gói Premium</a>
            <button type="button" onclick="window.print()">In hóa đơn</button>
        </footer>
    </article>
</main>

<style>
    .premium-invoice-wrap{max-width:900px;margin:42px auto;padding:0 18px;color:#f8fafc}.premium-invoice{overflow:hidden;border:1px solid #3c3157;border-radius:18px;background:#11131c;box-shadow:0 24px 70px #0005}.premium-invoice__header{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;padding:30px;background:linear-gradient(120deg,#26183e,#151522)}.premium-invoice__eyebrow{margin:0 0 8px;color:#c4b5fd;font-size:12px;font-weight:800;letter-spacing:.15em}.premium-invoice h1{margin:0;font-size:clamp(26px,4vw,36px)}.premium-invoice__lead{margin:8px 0 0;color:#cbd5e1}.premium-invoice__status{flex:0 0 auto;padding:8px 12px;border-radius:999px;background:#064e3b;color:#a7f3d0;font-size:13px;font-weight:800}.premium-invoice__notice{margin:20px 30px 0;padding:12px 14px;border-radius:9px;background:#064e3b;color:#a7f3d0}.premium-invoice__meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px;margin:0;padding:26px 30px;border-bottom:1px solid #2b303a}.premium-invoice__meta div{min-width:0}.premium-invoice__meta dt{margin-bottom:5px;color:#94a3b8;font-size:12px}.premium-invoice__meta dd{margin:0;overflow-wrap:anywhere;font-size:14px;font-weight:700}.premium-invoice__table-wrap{padding:8px 30px 22px}.premium-invoice__table{width:100%;border-collapse:collapse}.premium-invoice__table th,.premium-invoice__table td{padding:15px 4px;border-bottom:1px solid #2b303a;text-align:left}.premium-invoice__table th:last-child,.premium-invoice__table td:last-child{text-align:right;white-space:nowrap}.premium-invoice__table thead th{color:#94a3b8;font-size:12px;text-transform:uppercase;letter-spacing:.05em}.premium-invoice__discount td{color:#86efac}.premium-invoice__table tfoot th{padding-top:20px;border:0;font-size:16px}.premium-invoice__table tfoot th:last-child{color:#c4b5fd;font-size:20px}.premium-invoice__provider{margin:0;padding:0 30px 20px;color:#94a3b8;font-size:12px}.premium-invoice__provider strong{color:#cbd5e1}.premium-invoice__actions{display:flex;justify-content:flex-end;gap:10px;padding:20px 30px;border-top:1px solid #2b303a}.premium-invoice__actions a,.premium-invoice__actions button{padding:10px 14px;border:1px solid #51436c;border-radius:8px;background:#211832;color:#eee;text-decoration:none;font:inherit;font-size:13px;font-weight:700;cursor:pointer}.premium-invoice__actions button{background:#7c3aed;border-color:#7c3aed}@media(max-width:600px){.premium-invoice__header{flex-direction:column;padding:22px}.premium-invoice__meta{grid-template-columns:1fr;padding:20px 22px}.premium-invoice__table-wrap{padding:6px 22px 18px}.premium-invoice__actions{padding:16px 22px;flex-wrap:wrap}.premium-invoice__notice{margin:16px 22px 0}}@media print{body{background:#fff!important}.premium-invoice-wrap{max-width:none;margin:0;padding:0;color:#111}.premium-invoice{border:0;box-shadow:none;background:#fff;color:#111}.premium-invoice__header{background:#f5f3ff!important;color:#111;print-color-adjust:exact}.premium-invoice__lead,.premium-invoice__meta dt,.premium-invoice__provider{color:#555}.premium-invoice__actions,.premium-invoice__notice{display:none!important}.premium-invoice__meta,.premium-invoice__table th,.premium-invoice__table td{border-color:#ddd}.premium-invoice__discount td{color:#166534}}
</style>
@endsection
