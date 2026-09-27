@extends('admin.layouts.master')
@section('content')
<section id="storage" class="page storage-dashboard">
    <div class="page-head">
        <div>
            <h3>Storage cloud</h3>
            <p>Mỗi API Video Storage &amp; CDN hiển thị một kho lưu trữ riêng.</p>
        </div>
        <a class="btn" href="{{ route('admin.videos.upload') }}">+ Tải video</a>
    </div>

    @if(empty($storage['storages']))
        <div class="panel storage-empty-state">
            <div class="panel-body">
                <h4>Chưa có API Video Storage &amp; CDN</h4>
                <p>Thêm API ở mục Quản lý API để xem dung lượng từng kho cloud tại đây.</p>
            </div>
        </div>
    @else
        <p class="storage-count">{{ count($storage['storages']) }} API lưu trữ</p>
        <div class="storage-server-list">
            @foreach($storage['storages'] as $server)
                <article class="panel storage-server-card">
                    <div class="panel-head storage-server-head">
                        <div>
                            <h4>{{ $server['name'] }}</h4>
                            <small>{{ $server['endpoint_host'] ?? (parse_url((string) $server['endpoint'], PHP_URL_HOST) ?: '—') }} · Bucket: {{ $server['bucket'] }}</small>
                        </div>
                        @switch($server['status'])
                            @case('connected')<span class="storage-status is-connected">Đã kết nối</span>@break
                            @case('inactive')<span class="storage-status">Đang tắt</span>@break
                            @case('expired')<span class="storage-status is-unavailable">Đã hết hạn</span>@break
                            @case('incomplete')<span class="storage-status is-unavailable">Thiếu cấu hình</span>@break
                            @default<span class="storage-status is-unavailable">Không khả dụng</span>
                        @endswitch
                    </div>

                    @if($server['status'] === 'connected')
                        <div class="panel-body storage-server-body">
                            <div class="storage-metrics">
                                <div class="storage-metric is-used">
                                    <span class="storage-metric-icon" aria-hidden="true">▰</span>
                                    <strong>{{ $server['complete'] ? '' : '≥ ' }}{{ $server['formatted_size'] }}</strong>
                                    <small>Đã sử dụng</small>
                                </div>
                                <div class="storage-metric is-remaining">
                                    <span class="storage-metric-icon" aria-hidden="true">◷</span>
                                    <strong>Chưa có quota</strong>
                                    <small>API không trả dung lượng tối đa để tính phần còn lại</small>
                                </div>
                                <div class="storage-metric is-files">
                                    <span class="storage-metric-icon" aria-hidden="true">▤</span>
                                    <strong>{{ $server['complete'] ? number_format($server['files']) : '≥ ' . number_format($server['files']) }}</strong>
                                    <small>Object trên cloud</small>
                                </div>
                            </div>
                            <div class="storage-breakdown">
                                <div class="storage-breakdown-heading">
                                    <strong>Cơ cấu dung lượng</strong>
                                    <small>{{ $server['complete'] ? 'Theo tổng dung lượng bucket' : 'Tỷ trọng trong phần đã quét' }}</small>
                                </div>
                                <div class="storage-segment-bar" role="img" aria-label="Cơ cấu dung lượng theo loại file">
                                    @foreach(['videos' => 'Video / HLS', 'images' => 'Ảnh & posters', 'subtitles' => 'Phụ đề', 'other' => 'Loại khác'] as $key => $label)
                                        <span class="segment-{{ $key }}" style="width:{{ $server['categories'][$key]['percent'] ?? 0 }}%" title="{{ $label }}: {{ $server['categories'][$key]['formatted_size'] ?? '0,00 GB' }}"></span>
                                    @endforeach
                                </div>
                                @foreach([
                                    'videos' => 'Video / HLS',
                                    'images' => 'Ảnh & posters',
                                    'subtitles' => 'Phụ đề',
                                    'other' => 'Loại khác',
                                ] as $key => $label)
                                    <div class="storage-breakdown-row">
                                        <span class="storage-dot segment-{{ $key }}"></span>
                                        <span class="storage-breakdown-label">{{ $label }}<small>{{ $server['complete'] ? '' : '≥ ' }}{{ number_format($server['categories'][$key]['files']) }} object</small></span>
                                        <b>{{ $server['complete'] ? '' : '≥ ' }}{{ $server['categories'][$key]['formatted_size'] ?? '0,00 GB' }}</b>
                                        <small>{{ number_format($server['categories'][$key]['percent'] ?? 0, 1, ',', '.') }}%</small>
                                    </div>
                                @endforeach
                            </div>
                            <p class="storage-server-message">{{ $server['message'] }} Dữ liệu được cache tối đa 10 phút. Hạn mức lưu trữ Supabase phụ thuộc gói của tổ chức và không có trong API key storage.</p>
                        </div>
                    @else
                        <div class="panel-body"><p class="storage-server-message">{{ $server['message'] }}</p></div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>

<style>
.storage-dashboard .page-head{align-items:center}.storage-count{margin:0 0 12px;color:var(--muted,#9ca3af);font-size:12px}.storage-server-list{display:grid;gap:14px}.storage-server-card{overflow:hidden}.storage-server-head{display:flex;justify-content:space-between;align-items:center;gap:16px}.storage-server-head>div{display:grid;gap:5px;min-width:0}.storage-server-head h4{margin:0;font-size:16px}.storage-server-head small{color:var(--muted,#9ca3af);font-size:12px;overflow-wrap:anywhere}.storage-status{flex:none;padding:5px 10px;border:1px solid rgba(148,163,184,.3);border-radius:20px;color:#cbd5e1;font-size:11px}.storage-status.is-connected{border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.1);color:#86efac}.storage-status.is-unavailable{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.1);color:#fca5a5}.storage-server-body{display:grid;gap:20px}.storage-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.storage-metric{position:relative;display:grid;gap:7px;min-height:125px;padding:16px;border:1px solid rgba(148,163,184,.16);border-radius:12px;background:#171923}.storage-metric-icon{display:grid;place-items:center;width:30px;height:30px;border-radius:9px;background:rgba(167,139,250,.14);color:#c4b5fd;font-size:18px}.storage-metric strong{align-self:end;color:#f8fafc;font-size:clamp(17px,2vw,25px);line-height:1.15}.storage-metric small{color:var(--muted,#9ca3af);font-size:11px;line-height:1.45}.storage-metric.is-used{background:linear-gradient(135deg,rgba(124,58,237,.17),#171923 70%);border-color:rgba(167,139,250,.27)}.storage-metric.is-used strong{color:#c4b5fd}.storage-metric.is-remaining strong{font-size:17px}.storage-metric.is-files strong{color:#93c5fd}.storage-breakdown{padding:16px;border:1px solid rgba(148,163,184,.16);border-radius:12px;background:rgba(15,17,24,.5)}.storage-breakdown-heading{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px}.storage-breakdown-heading small{color:var(--muted,#9ca3af);font-size:11px}.storage-segment-bar{display:flex;height:13px;overflow:hidden;margin-bottom:15px;border-radius:99px;background:rgba(148,163,184,.14)}.storage-segment-bar span{min-width:0;transition:width .25s ease}.segment-videos{background:#8b5cf6}.segment-images{background:#38bdf8}.segment-subtitles{background:#34d399}.segment-other{background:#f59e0b}.storage-breakdown-row{display:grid;grid-template-columns:10px minmax(100px,1fr) auto 48px;align-items:center;gap:10px;padding:9px 0;border-top:1px solid rgba(148,163,184,.1);font-size:12px}.storage-dot{width:9px;height:9px;border-radius:50%}.storage-breakdown-label{display:grid;gap:3px}.storage-breakdown-label small,.storage-breakdown-row>small{color:var(--muted,#9ca3af);font-size:10px}.storage-breakdown-row>b{text-align:right;font-variant-numeric:tabular-nums}.storage-breakdown-row>small{text-align:right}.storage-server-message{margin:0;color:var(--muted,#9ca3af);font-size:11px;line-height:1.5}.storage-empty-state h4{margin:0 0 7px}.storage-empty-state p{margin:0;color:var(--muted,#9ca3af)}@media(max-width:700px){.storage-metrics{grid-template-columns:1fr}.storage-metric{min-height:auto}.storage-breakdown-heading{align-items:flex-start;flex-direction:column}.storage-breakdown-row{grid-template-columns:10px minmax(70px,1fr) auto}.storage-breakdown-row>small{display:none}}
</style>
@endsection
