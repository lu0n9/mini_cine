@extends('admin.layouts.master')

@section('content')

<section id="add-server" class="page">

    <div class="page-head">
        <div>
            <h3>Thêm video server</h3>
            <p>Kết nối nguồn phát video cho phim hoặc episode.</p>
        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.servers') }}"
        >
            ← Quay lại servers
        </a>
    </div>

    <div class="panel">
        <div class="panel-body">

            <form
                action="{{ route('admin.servers.store') }}"
                method="POST"
            >

                @csrf

                <div class="form-grid">

                    {{-- Tên server --}}
                    <div class="field">

                        <label>Tên server *</label>

                        <input
                            type="text"
                            name="server_name"
                            value="{{ old('server_name') }}"
                            placeholder="Ví dụ: Server VIP #2"
                            required
                        >

                        @error('server_name')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Loại nguồn --}}
                    <div class="field">

                        <label>Loại nguồn *</label>

                        <select
                            name="type"
                            required
                        >
                            <option value="">Chọn loại nguồn</option>

                            <option
                                value="hls"
                                {{ old('type') === 'hls' ? 'selected' : '' }}
                            >
                                HLS (.m3u8)
                            </option>

                            <option
                                value="dash"
                                {{ old('type') === 'dash' ? 'selected' : '' }}
                            >
                                DASH (.mpd)
                            </option>

                            <option
                                value="embed"
                                {{ old('type') === 'embed' ? 'selected' : '' }}
                            >
                                Embed URL
                            </option>

                            <option
                                value="mp4"
                                {{ old('type') === 'mp4' ? 'selected' : '' }}
                            >
                                MP4 trực tiếp
                            </option>

                        </select>

                        @error('type')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- URL nguồn --}}
                    <div class="field full">

                        <label>URL nguồn phát *</label>

                        <input
                            type="url"
                            name="source_url"
                            value="{{ old('source_url') }}"
                            placeholder="https://cdn.example.com/video/master.m3u8"
                            required
                        >

                        @error('source_url')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Chất lượng --}}
                    <div class="field">

                        <label>Chất lượng</label>

                        <select name="quality">

                            <option value="">
                                Chọn chất lượng
                            </option>

                            @foreach($qualities as $quality)

                                <option
                                    value="{{ $quality }}"
                                    {{ old('quality') === $quality ? 'selected' : '' }}
                                >
                                    {{ $quality }}
                                </option>

                            @endforeach

                        </select>

                        @error('quality')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Phim --}}
                    <div class="field">

                        <label>Phim *</label>

                        <select
                            name="movie_id"
                            id="movie_id"
                            required
                        >

                            <option value="">
                                Chọn phim
                            </option>

                            @foreach($movies as $movie)

                                <option
                                    value="{{ $movie->id }}"
                                    {{ old('movie_id') == $movie->id ? 'selected' : '' }}
                                >
                                    {{ $movie->title }}
                                </option>

                            @endforeach

                        </select>

                        @error('movie_id')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Episode --}}
                    <div class="field">

                        <label>Episode</label>

                        <select
                            name="episode_id"
                            id="episode_id"
                        >

                            <option value="">
                                Không gắn episode
                            </option>

                            @foreach($episodes as $episode)

                                <option
                                    value="{{ $episode->id }}"
                                    data-movie="{{ $episode->movie_id }}"
                                    {{ old('episode_id') == $episode->id ? 'selected' : '' }}
                                >
                                    Tập {{ $episode->episode_number }}
                                    @if($episode->name)
                                        — {{ $episode->name }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                        @error('episode_id')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Trạng thái --}}
                    <div class="setting-row field full">

                        <div class="st-txt">

                            <strong>Trạng thái server</strong>

                            <small>
                                Cho phép nguồn phát này được sử dụng.
                            </small>

                        </div>

                        <label class="toggle">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                            >

                            <span class="track"></span>

                        </label>

                    </div>


                    {{-- Actions --}}
                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.servers') }}"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Lưu server
                        </button>

                    </div>

                </div>

            </form>

        </div>
    </div>

</section>

@endsection