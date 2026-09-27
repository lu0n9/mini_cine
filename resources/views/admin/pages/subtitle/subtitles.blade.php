@extends('admin.layouts.master')

@section('content')

<section id="subtitles" class="page">

    <div class="page-head">
        <div>
            <h3>Subtitles</h3>
            <p>Upload và quản lý phụ đề (.srt / .vtt) theo tập và ngôn ngữ.</p>
        </div>

        <a
            href="{{ route('admin.subtitles.create') }}"
            class="btn"
        >
            + Upload subtitle
        </a>
    </div>


    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.subtitles') }}" class="table-tools">
        <span class="mini-search">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm phim, tập, ngôn ngữ...">
        </span>
        <select class="filter" name="movie_id" aria-label="Lọc theo phim">
            <option value="">Tất cả phim</option>
            @foreach($movies as $movie)
                <option value="{{ $movie->id }}" @selected((string) request('movie_id') === (string) $movie->id)>{{ $movie->title }}</option>
            @endforeach
        </select>
        <select class="filter" name="language" aria-label="Lọc theo ngôn ngữ">
            <option value="">Tất cả ngôn ngữ</option>
            @foreach($languages as $language)
                <option value="{{ $language }}" @selected(request('language') === $language)>{{ $language }}</option>
            @endforeach
        </select>
        <select class="filter" name="active" aria-label="Lọc theo trạng thái">
            <option value="">Mọi trạng thái</option>
            <option value="1" @selected(request('active') === '1')>Đang bật</option>
            <option value="0" @selected(request('active') === '0')>Đang tắt</option>
        </select>
        <select class="filter" name="default" aria-label="Lọc phụ đề mặc định">
            <option value="">Mặc định hoặc khác</option>
            <option value="1" @selected(request('default') === '1')>Phụ đề mặc định</option>
            <option value="0" @selected(request('default') === '0')>Không mặc định</option>
        </select>
        <button type="submit" class="filter on">Lọc</button>
        <a href="{{ route('admin.subtitles') }}" class="filter">Xóa lọc</a>
    </form>

    <div class="panel">

        <table>

            <thead>
                <tr>
                    <th>Tập</th>
                    <th>Ngôn ngữ</th>
                    <th>Label</th>
                    <th>Định dạng</th>
                    <th>Mặc định</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse($subtitles as $subtitle)

                    <tr>

                        {{-- Tập phim --}}
                        <td>
                            @if($subtitle->episode)

                                {{ $subtitle->episode->movie->title ?? '—' }}
                                · Tập {{ $subtitle->episode->episode_number }}

                            @else

                                —

                            @endif
                        </td>


                        {{-- Ngôn ngữ --}}
                        <td>
                            {{ $subtitle->language }}
                        </td>


                        {{-- Label --}}
                        <td>
                            {{ $subtitle->label }}
                        </td>


                        {{-- Định dạng --}}
                        <td>
                            .{{ $subtitle->format }}
                        </td>


                        {{-- Mặc định --}}
                        <td>

                            @if($subtitle->is_default)

                                <span class="tag solid">
                                    Mặc định
                                </span>

                            @else

                                —

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td>

                            <div class="row-actions">

                                {{-- Edit --}}
                                <a
                                    href="{{ route('admin.subtitles.edit', $subtitle->id) }}"
                                    class="mini"
                                >
                                    ✎
                                </a>


                                {{-- Delete --}}
                                <form
                                    action="{{ route('admin.subtitles.destroy', $subtitle->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa subtitle này?')"
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
                        <td colspan="6">
                            Chưa có subtitle nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($subtitles->hasPages())
        <div class="pagination">
            {{ $subtitles->links('pagination::custom') }}
        </div>
    @endif

</section>

@endsection
