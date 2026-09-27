@extends('admin.layouts.master')
@section('content')
<section id="cache" class="page cache-dashboard">
    <div class="page-head">
        <div>
            <h3>Cache ứng dụng</h3>
            <p>Xem trạng thái cache và xóa từng loại khi cần.</p>
        </div>
        <span class="cache-store-badge">Store: {{ $cacheStatus['store'] }} · {{ $cacheStatus['driver'] }}</span>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="cache-status-grid">
        <article class="panel cache-status-card">
            <div class="panel-body">
                <span class="cache-status-icon is-purple" aria-hidden="true">◈</span>
                <small>Application cache</small>
                <strong>{{ $cacheStatus['item_count'] === null ? '—' : number_format($cacheStatus['item_count']) }}</strong>
                <span>{{ $cacheStatus['size'] ?? 'Không có thống kê cho driver này' }}</span>
            </div>
        </article>
        <article class="panel cache-status-card">
            <div class="panel-body">
                <span class="cache-status-icon is-blue" aria-hidden="true">⚙</span>
                <small>Config cache</small>
                <strong>{{ $cacheStatus['config_cached'] ? 'Đang bật' : 'Đang tắt' }}</strong>
                <span>{{ $cacheStatus['config_cached'] ? 'Đang dùng file cấu hình đã biên dịch' : 'Laravel đọc cấu hình trực tiếp' }}</span>
            </div>
        </article>
        <article class="panel cache-status-card">
            <div class="panel-body">
                <span class="cache-status-icon is-green" aria-hidden="true">⌘</span>
                <small>Route cache</small>
                <strong>{{ $cacheStatus['route_cached'] ? 'Đang bật' : 'Đang tắt' }}</strong>
                <span>{{ $cacheStatus['route_cached'] ? 'Đang dùng route đã biên dịch' : 'Laravel nạp route trực tiếp' }}</span>
            </div>
        </article>
        <article class="panel cache-status-card">
            <div class="panel-body">
                <span class="cache-status-icon is-orange" aria-hidden="true">▧</span>
                <small>View cache</small>
                <strong>{{ number_format($cacheStatus['view_count']) }} file</strong>
                <span>{{ $cacheStatus['view_size'] }} template đã biên dịch</span>
            </div>
        </article>
    </div>

    <div class="panel cache-actions-panel">
        <div class="panel-head"><h4>Xóa cache</h4></div>
        <div class="panel-body cache-actions">
            @foreach([
                'application' => ['Application cache', 'Xóa dữ liệu trong cache store đang cấu hình.'],
                'config' => ['Config cache', 'Xóa file cấu hình đã biên dịch của Laravel.'],
                'route' => ['Route cache', 'Xóa route đã biên dịch của Laravel.'],
                'view' => ['View cache', 'Xóa các template Blade đã biên dịch.'],
            ] as $target => [$label, $description])
                <div class="cache-action-row">
                    <div><strong>{{ $label }}</strong><small>{{ $description }}</small></div>
                    <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="target" value="{{ $target }}">
                        <button class="btn ghost sm" type="submit">Xóa</button>
                    </form>
                </div>
            @endforeach
            <div class="cache-action-row cache-action-all">
                <div><strong>Xóa toàn bộ cache</strong><small>Xóa application, config, route, view và event cache.</small></div>
                <form method="POST" action="{{ route('admin.system.cache.clear-all') }}" onsubmit="return confirm('Bạn muốn xóa toàn bộ cache Laravel?')">
                    @csrf
                    <button class="btn sm" type="submit">Xóa tất cả</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.cache-dashboard .page-head{align-items:center}.cache-store-badge{padding:7px 11px;border:1px solid rgba(167,139,250,.28);border-radius:20px;background:rgba(124,58,237,.1);color:#c4b5fd;font-size:11px}.cache-status-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:16px 0}.cache-status-card .panel-body{display:grid;align-content:start;gap:8px;min-height:155px}.cache-status-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:10px;font-size:18px}.cache-status-icon.is-purple{background:rgba(139,92,246,.16);color:#c4b5fd}.cache-status-icon.is-blue{background:rgba(59,130,246,.15);color:#93c5fd}.cache-status-icon.is-green{background:rgba(34,197,94,.13);color:#86efac}.cache-status-icon.is-orange{background:rgba(245,158,11,.13);color:#fcd34d}.cache-status-card small,.cache-status-card>span{color:var(--muted,#9ca3af);font-size:11px}.cache-status-card strong{font-size:20px}.cache-actions-panel{overflow:hidden}.cache-actions-panel .panel-head h4{margin:0}.cache-actions{padding-top:0}.cache-action-row{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px 0;border-bottom:1px solid var(--line,#2b303a)}.cache-action-row>div{display:grid;gap:5px}.cache-action-row strong{font-size:13px}.cache-action-row small{color:var(--muted,#9ca3af);font-size:11px}.cache-action-row form{flex:none}.cache-action-all{margin-top:5px;padding:16px;border:1px solid rgba(239,68,68,.22);border-radius:10px;background:rgba(127,29,29,.08)}@media(max-width:900px){.cache-status-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.cache-dashboard .page-head{align-items:flex-start;flex-direction:column}.cache-status-grid{grid-template-columns:1fr 1fr}.cache-status-card .panel-body{min-height:140px;padding:13px}.cache-action-row{align-items:flex-start}.cache-action-row>div{max-width:75%}.cache-action-row .btn{min-width:60px}}@media(max-width:380px){.cache-status-grid{grid-template-columns:1fr}}
</style>
@endsection
