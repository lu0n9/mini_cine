@extends('admin.layouts.master')

@section('content')

<section id="episodes" class="page">

    <div class="page-head">
        <div>
            <h3>Episodes</h3>
            <p>Quản lý tập phim: thêm, sửa, sắp xếp, đánh dấu VIP/mới.</p>
        </div>

        <a href="{{ route('admin.episodes.create') }}" class="btn">
            + Thêm tập
        </a>
    </div>

    <form method="GET" action="{{ route('admin.episodes') }}" class="table-tools">
        <span class="mini-search">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tập hoặc phim...">
        </span>
        <select class="filter" name="movie_id" aria-label="Lọc theo phim">
            <option value="">Tất cả phim</option>
            @foreach($movies as $movie)
                <option value="{{ $movie->id }}" @selected((string) request('movie_id') === (string) $movie->id)>{{ $movie->title }}</option>
            @endforeach
        </select>
        <select class="filter" name="season_id" aria-label="Lọc theo season">
            <option value="">Tất cả season</option>
            @foreach($seasons as $season)
                <option value="{{ $season->id }}" @selected((string) request('season_id') === (string) $season->id)>{{ $season->movie->title ?? 'Phim' }} · {{ $season->name ?: 'Season ' . $season->season_number }}</option>
            @endforeach
        </select>
        <select class="filter" name="published" aria-label="Lọc trạng thái xuất bản">
            <option value="">Mọi trạng thái</option>
            <option value="1" @selected(request('published') === '1')>Đã xuất bản</option>
            <option value="0" @selected(request('published') === '0')>Chưa xuất bản</option>
        </select>
        <button type="submit" class="filter on">Lọc</button>
        <a href="{{ route('admin.episodes') }}" class="filter">Xóa lọc</a>
    </form>

    <div class="panel">
        <table>

            <thead>
                <tr>
                    <th>Tập</th>
                    <th>Phim / Season</th>
                    <th>Thời lượng</th>
                    <th>Phát hành</th>
                    <th>Nhãn</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse($episodes as $episode)

                    <tr>

                        {{-- Tập --}}
                        <td>
                            Tập {{ $episode->episode_number }}

                            @if($episode->name)
                                — {{ $episode->name }}
                            @endif
                        </td>

                        {{-- Phim / Season --}}
                        <td>
                            {{ $episode->movie->title ?? '—' }}

                            @if($episode->season)
                                · S{{ $episode->season->season_number }}
                            @endif
                        </td>

                        {{-- Thời lượng --}}
                        <td>
                            @if($episode->duration)
                                {{ floor($episode->duration / 60) }}:{{ str_pad($episode->duration % 60, 2, '0', STR_PAD_LEFT) }}
                            @else
                                —
                            @endif
                        </td>

                        {{-- Ngày phát hành --}}
                        <td>
                            @if($episode->release_date)
                                {{ $episode->release_date->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>

                        {{-- Nhãn --}}
                        <td>

                            @if($episode->is_new)

                                <span class="tag solid">
                                    Mới
                                </span>

                            @elseif($episode->is_vip)

                                <span class="tag">
                                    VIP
                                </span>

                            @else

                                —

                            @endif

                        </td>

                        {{-- Trạng thái --}}
                        <td>

                            @if($episode->is_active)

                                <span class="status">
                                    Hiển thị
                                </span>

                            @else

                                <span class="status off">
                                    Ẩn
                                </span>

                            @endif

                        </td>

                        {{-- Actions --}}
                        <td>

                            <div class="row-actions">

                                {{-- Sửa --}}
                                <a
                                    href="{{ route('admin.episodes.edit', $episode->id) }}"
                                    class="mini"
                                >
                                    ✎
                                </a>

                                {{-- Xóa --}}
                                <form
                                    action="{{ route('admin.episodes.destroy', $episode->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa tập phim này?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                    >
                                        ✕
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Chưa có tập phim nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

    @if($episodes->hasPages())
        <div class="pagination">
            {{ $episodes->links('pagination::custom') }}
        </div>
    @endif

</section>

@endsection
