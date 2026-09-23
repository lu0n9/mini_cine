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

</section>

@endsection