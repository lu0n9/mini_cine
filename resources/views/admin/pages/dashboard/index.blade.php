@extends('admin.layouts.master')
@section('content')
<section id="dashboard" class="page">
    <div class="page-head">
        <div>
            <h3>Bảng điều khiển</h3>
            <p>Tổng quan dữ liệu thực tế của nền tảng đến {{ now()->format('d/m/Y H:i') }}.</p>
        </div>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="top"><span class="label">Tổng phim</span><span class="chip">▣</span></div>
            <div class="num">{{ number_format($totalMovies) }}</div>
            <div class="delta"><b>+{{ number_format($moviesThisMonth) }}</b> phim trong tháng này</div>
        </div>
        <div class="stat">
            <div class="top"><span class="label">Tổng tập phim</span><span class="chip">▤</span></div>
            <div class="num">{{ number_format($totalEpisodes) }}</div>
            <div class="delta"><b>+{{ number_format($episodesThisWeek) }}</b> tập trong tuần này</div>
        </div>
        <div class="stat">
            <div class="top"><span class="label">Người dùng</span><span class="chip">♙</span></div>
            <div class="num">{{ number_format($totalUsers) }}</div>
            <div class="delta"><b>+{{ number_format($usersThisMonth) }}</b> người mới trong tháng</div>
        </div>
        <div class="stat">
            <div class="top"><span class="label">Lượt xem hôm nay</span><span class="chip">◉</span></div>
            <div class="num">{{ number_format($viewsToday) }}</div>
            <div class="delta"><b>{{ $viewsChange > 0 ? '+' : '' }}{{ $viewsChange }}%</b> so với hôm qua</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="panel">
            <div class="panel-head"><h4>Lượt xem theo tháng · {{ now()->year }}</h4><span class="legend"><span><i class="d"></i>Lượt xem</span></span></div>
            <div class="panel-body">
                <div class="chart">
                    @foreach($monthlyViews as $month)
                        <div class="bar-wrap" title="{{ $month['label'] }}: {{ number_format($month['total']) }} lượt xem">
                            <div class="bar" style="height: {{ $month['height'] }}%"></div>
                            <span class="bar-x">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                @if($monthlyViews->sum('total') === 0)
                    <p class="hint" style="text-align:center">Chưa có lượt xem trong năm nay.</p>
                @endif
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h4>Hoạt động gần đây</h4></div>
            <div class="panel-body activity">
                @forelse($activities as $activity)
                    <div class="act">
                        <div class="dot">{{ $activity['icon'] }}</div>
                        <div class="txt"><b>{{ $activity['title'] }}</b> {{ $activity['description'] }}
                            <small>{{ $activity['created_at']?->diffForHumans() ?? 'Thời gian không xác định' }}</small>
                        </div>
                    </div>
                @empty
                    <p class="hint">Chưa có hoạt động nào để hiển thị.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><h4>Phim được xem nhiều nhất</h4><span class="hint">Xếp theo lượt mở trang xem</span></div>
        <div class="panel-body">
            @if($popularMovies->isNotEmpty())
                <div class="poster-grid">
                    @foreach($popularMovies as $index => $movie)
                        @php
                            $posterUrl = $movie->poster
                                ? (\Illuminate\Support\Str::startsWith($movie->poster, ['http://', 'https://'])
                                    ? $movie->poster
                                    : asset('Storage/' . ltrim($movie->poster, '/')))
                                : null;
                        @endphp
                        <div class="poster">
                            <div class="pic" style="background:linear-gradient(135deg,#1e293b,#312e81)">
                                <span class="rk">#{{ $index + 1 }}</span>
                                @if($posterUrl)
                                    <img src="{{ $posterUrl }}" alt="{{ $movie->title }}" loading="lazy" onerror="this.style.display='none'">
                                @else
                                    <div style="height:100%;display:grid;place-items:center;padding:12px;text-align:center;color:#cbd5e1">{{ $movie->title }}</div>
                                @endif
                            </div>
                            <div class="meta">
                                <h5>{{ $movie->title }}</h5>
                                <div class="sub">
                                    <span>{{ $movie->genres->first()?->name ?? ($movie->type === 'series' ? 'Phim bộ' : 'Phim lẻ') }}</span>
                                    <span>{{ number_format($movie->views_count) }} lượt xem</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="hint">Chưa có phim trong hệ thống.</p>
            @endif
        </div>
    </div>
</section>
@endsection
