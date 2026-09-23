@extends('admin.layouts.master')

@section('content')

<section id="edit-server" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa video server</h3>
            <p>Cập nhật nguồn phát video cho phim hoặc episode.</p>
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
                action="{{ route('admin.servers.update', $source->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                <div class="form-grid">

                    {{-- Tên server --}}
                    <div class="field">

                        <label>Tên server *</label>

                        <input
                            type="text"
                            name="server_name"
                            value="{{ old('server_name', $source->server_name) }}"
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

                            <option value="">
                                Chọn loại nguồn
                            </option>

                            @foreach($types as $type)

                                <option
                                    value="{{ $type }}"
                                    {{ old('type', $source->type) === $type ? 'selected' : '' }}
                                >
                                    @if($type === 'hls')
                                        HLS (.m3u8)
                                    @elseif($type === 'dash')
                                        DASH (.mpd)
                                    @elseif($type === 'embed')
                                        Embed URL
                                    @elseif($type === 'mp4')
                                        MP4 trực tiếp
                                    @else
                                        {{ strtoupper($type) }}
                                    @endif
                                </option>

                            @endforeach

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
                            value="{{ old('source_url', $source->source_url) }}"
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
                                    {{ old('quality', $source->quality) === $quality ? 'selected' : '' }}
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
                                    {{ old('movie_id', $source->movie_id) == $movie->id ? 'selected' : '' }}
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
                                    {{ old('episode_id', $source->episode_id) == $episode->id ? 'selected' : '' }}
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
                                {{ old('is_active', $source->is_active) ? 'checked' : '' }}
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
                            Cập nhật server
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</section>


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const movieSelect = document.getElementById('movie_id');
    const episodeSelect = document.getElementById('episode_id');

    if (!movieSelect || !episodeSelect) {
        return;
    }


    function filterEpisodes() {

        const movieId = movieSelect.value;

        Array.from(episodeSelect.options).forEach(function (option) {

            // Option "Không gắn episode"
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.movie !== movieId;

        });


        // Kiểm tra episode hiện tại
        const selectedOption =
            episodeSelect.options[episodeSelect.selectedIndex];

        if (
            selectedOption &&
            selectedOption.value &&
            selectedOption.dataset.movie !== movieId
        ) {
            episodeSelect.value = '';
        }

    }


    movieSelect.addEventListener('change', filterEpisodes);

    filterEpisodes();

});

</script>

@endpush

@endsection