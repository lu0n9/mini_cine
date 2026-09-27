@extends('admin.layouts.master')

@section('content')
<section id="cron" class="page">
    <div class="page-head"><div><h3>Cron Jobs</h3><p>Tác vụ định kỳ của hệ thống.</p></div></div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom:16px">{{ session('error') }}</div>
    @endif

    <div class="panel"><table>
        <thead><tr><th>Job</th><th>Lịch</th><th>Lần chạy cuối</th><th>Trạng thái</th><th></th></tr></thead>
        <tbody>
            @foreach ($definitions as $key => $definition)
                @php($run = $runs->get($key))
                <tr>
                    <td>
                        {{ $definition['name'] }}
                        @if ($run?->message)<small style="display:block;color:#7b8495;margin-top:3px">{{ $run->message }}</small>@endif
                    </td>
                    <td>{{ $definition['schedule'] }}</td>
                    <td>{{ $run?->last_run_at?->timezone(config('app.timezone'))->diffForHumans() ?? 'Chưa chạy' }}</td>
                    <td>
                        @if (!$run)
                            <span class="status warn">Chưa chạy</span>
                        @elseif ($run->status === 'success')
                            <span class="status">OK</span>
                        @else
                            <span class="status warn">Lỗi</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <form method="POST" action="{{ route('admin.system.cron.run', $key) }}" onsubmit="return confirm('Chạy tác vụ {{ $definition['name'] }} ngay bây giờ?')">
                                @csrf
                                <button class="mini" type="submit" title="Chạy ngay" aria-label="Chạy ngay">▶</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table></div>
</section>
@endsection
