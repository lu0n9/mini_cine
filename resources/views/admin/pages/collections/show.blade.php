@extends('admin.layouts.master')

@section('content')

<section id="collection-show" class="page">


    {{-- HEADER --}}
    <div class="page-head">

        <div>

            <h3>
                {{ $collection->name }}
            </h3>

            <p>
                {{ $collection->description ?: 'Danh sách phim thuộc Collection này.' }}
            </p>

        </div>


        <div
            style="
                display:flex;
                gap:8px;
                flex-wrap:wrap;
            "
        >

            <a
                href="{{ route('admin.collections') }}"
                class="btn ghost"
            >
                ← Quay lại
            </a>


            <a
                href="{{ route('admin.collections.edit', $collection) }}"
                class="btn"
            >
                ✎ Sửa Collection
            </a>

        </div>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}
    @if(session('error'))

        <div class="alert error">
            {{ session('error') }}
        </div>

    @endif


    {{-- COLLECTION INFO --}}
    <div
        class="panel"
        style="margin-bottom:20px;"
    >

        <div class="panel-body">

            <div
                style="
                    display:grid;
                    grid-template-columns:minmax(180px,240px) 1fr;
                    gap:24px;
                    align-items:start;
                "
            >

                {{-- Poster --}}
                <div>

                    @if($collection->poster)

                        <img
                            src="{{ asset('storage/' . $collection->poster) }}"
                            alt="{{ $collection->name }}"
                            style="
                                display:block;
                                width:100%;
                                aspect-ratio:2/3;
                                object-fit:cover;
                                border-radius:10px;
                            "
                        >

                    @else

                        <div
                            style="
                                width:100%;
                                aspect-ratio:2/3;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                border-radius:10px;
                                background:rgba(255,255,255,.05);
                            "
                        >
                            Không có Poster
                        </div>

                    @endif

                </div>


                {{-- Information --}}
                <div>

                    <h4 style="margin-bottom:15px;">
                        Thông tin Collection
                    </h4>


                    <p class="hint">
                        <strong>Tên:</strong>
                        {{ $collection->name }}
                    </p>


                    <p class="hint">
                        <strong>Slug:</strong>
                        {{ $collection->slug }}
                    </p>


                    <p class="hint">
                        <strong>Số phim:</strong>
                        {{ $collection->movies->count() }}
                    </p>


                    <p class="hint">
                        <strong>Trạng thái:</strong>

                        @if($collection->is_active)

                            <span class="tag solid">
                                Đang hoạt động
                            </span>

                        @else

                            <span class="tag">
                                Tạm ẩn
                            </span>

                        @endif

                    </p>


                    @if($collection->description)

                        <div style="margin-top:20px;">

                            <h4>
                                Mô tả
                            </h4>

                            <p class="hint">
                                {{ $collection->description }}
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- MOVIES --}}
    <div class="page-head">

        <div>

            <h3>
                Phim trong Collection
            </h3>

            <p>
                {{ $collection->movies->count() }} phim
                đang thuộc Collection này.
            </p>

        </div>

    </div>


    <div class="panel">

        <div class="panel-body">

            @if($collection->movies->count())

                <div
                    style="
                        overflow-x:auto;
                    "
                >

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Phim
                                </th>

                                <th>
                                    Thể loại
                                </th>

                                <th>
                                    Loại
                                </th>

                                <th>
                                    Lượt xem
                                </th>

                                <th>
                                    Trạng thái
                                </th>

                                <th>
                                    #
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($collection->movies as $movie)

                                <tr>

                                    {{-- Movie --}}
                                    <td>

                                        <div
                                            style="
                                                display:flex;
                                                align-items:center;
                                                gap:12px;
                                                min-width:260px;
                                            "
                                        >

                                            @if($movie->poster)

                                                <img
                                                    src="{{ asset('storage/' . $movie->poster) }}"
                                                    alt="{{ $movie->title }}"
                                                    style="
                                                        width:55px;
                                                        height:78px;
                                                        object-fit:cover;
                                                        border-radius:6px;
                                                    "
                                                >

                                            @endif


                                            <div>

                                                <strong>
                                                    {{ $movie->title }}
                                                </strong>

                                                @if($movie->release_year)

                                                    <div class="hint">
                                                        {{ $movie->release_year }}
                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Genres --}}
                                    <td>

                                        @forelse($movie->genres->take(3) as $genre)

                                            <span class="tag">
                                                {{ $genre->name }}
                                            </span>

                                        @empty

                                            <span class="hint">
                                                Chưa có
                                            </span>

                                        @endforelse

                                    </td>


                                    {{-- Type --}}
                                    <td>

                                        @if($movie->type === 'series')

                                            Phim bộ

                                        @else

                                            Phim lẻ

                                        @endif

                                    </td>


                                    {{-- Views --}}
                                    <td>

                                        {{ number_format($movie->views_count ?? 0) }}

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if($movie->is_published)

                                            <span class="tag solid">
                                                Đã xuất bản
                                            </span>

                                        @else

                                            <span class="tag">
                                                Bản nháp
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td>

                                        <div
                                            class="row-actions"
                                            style="
                                                display:flex;
                                                gap:6px;
                                            "
                                        >

                                            {{-- Xem phim --}}
                                            <a
                                                href="{{ route('movie.detail', ['slug' => $movie->slug]) }}"
                                                class="mini"
                                                title="Xem phim"
                                            >
                                                👁
                                            </a>


                                            {{-- Xóa khỏi Collection --}}
                                            <form
                                                action="{{ route(
                                                    'admin.collections.movies.remove',
                                                    [
                                                        'collection' => $collection,
                                                        'movie' => $movie->id
                                                    ]
                                                ) }}"
                                                method="POST"
                                                onsubmit="return confirm('Xóa phim này khỏi Collection? Phim sẽ không bị xóa khỏi hệ thống.')"
                                                style="display:inline;"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="mini"
                                                    title="Xóa khỏi Collection"
                                                >
                                                    ✕
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div
                    style="
                        text-align:center;
                        padding:40px 20px;
                    "
                >

                    <p>
                        Collection này chưa có phim nào.
                    </p>

                    <p class="hint">
                        Bạn có thể thêm phim vào Collection từ chức năng quản lý phim.
                    </p>

                </div>

            @endif

        </div>

    </div>


</section>

@endsection