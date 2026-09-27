@extends('admin.layouts.master')

@section('content')
<section id="logs" class="page">
    <div class="page-head">
        <div>
            <h3>Activity Logs</h3>
            <p>Lịch sử các thao tác tạo, cập nhật, xóa và khôi phục trong trang quản trị.</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:18px">
        <div class="panel" style="padding:18px">
            <div style="color:#7b8495;font-size:13px">Thao tác hôm nay</div>
            <strong style="display:block;font-size:26px;margin-top:5px">{{ number_format($todayCount) }}</strong>
        </div>
        <div class="panel" style="padding:18px">
            <div style="color:#7b8495;font-size:13px">Quản trị viên hoạt động hôm nay</div>
            <strong style="display:block;font-size:26px;margin-top:5px">{{ number_format($adminCount) }}</strong>
        </div>
    </div>

    <div class="panel" style="padding:16px;margin-bottom:18px">
        <form method="GET" action="{{ route('admin.system.activity-log') }}" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap">
            <label style="display:grid;gap:6px;min-width:220px;flex:1">
                <span>Tìm kiếm</span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên, email, hành động, đối tượng hoặc IP">
            </label>
            <label style="display:grid;gap:6px;min-width:165px">
                <span>Loại thao tác</span>
                <select name="action">
                    <option value="">Tất cả</option>
                    @foreach (['Tạo / thực hiện', 'Cập nhật', 'Đổi trạng thái', 'Xóa', 'Xóa cache', 'Khôi phục'] as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </label>
            <label style="display:grid;gap:6px">
                <span>Từ ngày</span>
                <input type="date" name="from" value="{{ request('from') }}">
            </label>
            <label style="display:grid;gap:6px">
                <span>Đến ngày</span>
                <input type="date" name="to" value="{{ request('to') }}">
            </label>
            <button class="btn" type="submit">Lọc</button>
            <a class="btn" href="{{ route('admin.system.activity-log') }}">Xóa lọc</a>
        </form>
    </div>

    <div class="panel">
        <div style="padding:18px 20px 0;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap">
            <h4 style="margin:0">Nhật ký quản trị</h4>
            <span style="color:#7b8495">{{ number_format($logs->total()) }} kết quả</span>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr><th>Người thực hiện</th><th>Hành động</th><th>Đối tượng / trang</th><th>IP</th><th>Kết quả</th><th>Thời gian</th></tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->admin_name }}</strong>
                                @if ($log->admin_email)<small style="display:block;color:#7b8495">{{ $log->admin_email }}</small>@endif
                            </td>
                            <td>{{ $log->action }}</td>
                            <td><code>{{ $log->subject ?: $log->route_name }}</code></td>
                            <td>{{ $log->ip_address ?: '—' }}</td>
                            <td>{{ $log->status_code ?: '—' }}</td>
                            <td>{{ $log->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:28px;color:#7b8495">Chưa có nhật ký phù hợp. Các thao tác quản trị thành công sẽ được ghi từ khi bật chức năng này.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div style="padding:16px 20px">{{ $logs->links() }}</div>
        @endif
    </div>
</section>
@endsection
