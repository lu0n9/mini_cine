@extends('admin.layouts.master')

@section('content')

<section id="servers" class="page">

    <div class="page-head">
        <div>
            <h3>Video Servers</h3>
            <p>Quản lý nguồn phát: embed, HLS (.m3u8), DASH (.mpd).</p>
        </div>

        <a href="{{ route('admin.servers.create') }}" class="btn">
            + Thêm server
        </a>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Tên server</th>
                    <th>Phim / Episode</th>
                    <th>Loại</th>
                    <th>Chất lượng</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse($sources as $source)

                    <tr>

                        {{-- Tên server --}}
                        <td>
                            {{ $source->server_name }}
                        </td>

                        {{-- Phim / Episode --}}
                        <td>

                            @if($source->movie)
                                {{ $source->movie->title }}
                            @else
                                —
                            @endif

                            @if($source->episode)
                                <br>

                                <small>
                                    Tập {{ $source->episode->episode_number }}

                                    @if($source->episode->name)
                                        — {{ $source->episode->name }}
                                    @endif
                                </small>
                            @endif

                        </td>

                        {{-- Loại --}}
                        <td>

                            @if($source->type === 'hls')
                                HLS (.m3u8)

                            @elseif($source->type === 'dash')
                                DASH (.mpd)

                            @elseif($source->type === 'embed')
                                Embed

                            @elseif($source->type === 'mp4')
                                MP4

                            @else
                                {{ strtoupper($source->type) }}
                            @endif

                        </td>

                        {{-- Chất lượng --}}
                        <td>
                            {{ $source->quality ?: '—' }}
                        </td>

                        {{-- Trạng thái --}}
                        <td>

                            @if($source->is_active)
                                <span class="status">
                                    Bật
                                </span>
                            @else
                                <span class="status off">
                                    Tắt
                                </span>
                            @endif

                        </td>

                        {{-- Action --}}
                        <td>

                            <div class="row-actions">

                                {{-- Sửa --}}
                                <a
                                    href="{{ route('admin.servers.edit', $source->id) }}"
                                    class="mini"
                                >
                                    ✎
                                </a>

                                {{-- Xóa --}}
                                <form
                                    action="{{ route('admin.servers.destroy', $source->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa server này?')"
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
                        <td
                            colspan="6"
                            style="text-align: center;"
                        >
                            Chưa có server nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</section>

@endsection