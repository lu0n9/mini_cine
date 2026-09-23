@extends('admin.layouts.master')

@section('content')

<section id="seasons" class="page">

    {{-- ================= HEADER ================= --}}
    <div class="page-head">

        <div>
            <h3>Seasons</h3>
            <p>
                Quản lý mùa phim cho các phim bộ và series.
            </p>
        </div>

        <a
            href="{{ route('admin.seasons.create') }}"
            class="btn"
        >
            + Thêm season
        </a>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="panel">

        <table>

            <thead>
                <tr>
                    <th>Phim</th>
                    <th>Season</th>
                    <th>Số tập</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>


            <tbody>

                @forelse($seasons as $season)

                    <tr>

                        {{-- PHIM --}}
                        <td>
                            {{ $season->movie->title ?? 'Không xác định' }}
                        </td>


                        {{-- SEASON --}}
                        <td>
                            {{ $season->name }}
                        </td>


                        {{-- SỐ TẬP --}}
                        <td>
                            {{ $season->episodes_count }}
                        </td>


                        {{-- THỨ TỰ --}}
                        <td>
                            {{ $season->season_number }}
                        </td>


                        {{-- TRẠNG THÁI --}}
                        <td>

                            @if($season->is_published)

                                <span class="status">
                                    Hiển thị
                                </span>

                            @else

                                <span class="status off">
                                    Ẩn
                                </span>

                            @endif

                        </td>


                        {{-- ACTION --}}
                        <td>

                            <div class="row-actions">

                                {{-- EDIT --}}
                                <a
                                    href="{{ route('admin.seasons.edit', $season->id) }}"
                                    class="mini"
                                    title="Sửa"
                                >
                                    ✎
                                </a>


                                {{-- DELETE --}}
                                <form
                                    action="{{ route('admin.seasons.destroy', $season->id) }}"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa season này không?');"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="Xóa"
                                    >
                                        ✕
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="
                                text-align:center;
                                padding:40px 20px;
                                color:#888;
                            "
                        >
                            Chưa có season nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>

@endsection