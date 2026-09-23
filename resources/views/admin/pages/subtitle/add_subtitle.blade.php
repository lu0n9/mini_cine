@extends('admin.layouts.master')

@section('content')

<section id="add-subtitle" class="page">

    <div class="page-head">

        <div>
            <h3>Thêm subtitle</h3>

            <p>
                Thêm phụ đề cho phim hoặc episode bằng link Cloud.
            </p>
        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.subtitles') }}"
        >
            ← Quay lại subtitles
        </a>

    </div>


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.subtitles.store') }}"
                method="POST"
            >

                @csrf

                <div class="form-grid">


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

                        <label>Episode *</label>

                        <select
                            name="episode_id"
                            id="episode_id"
                            required
                        >

                            <option value="">
                                Chọn episode
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


                    {{-- Ngôn ngữ --}}

                    <div class="field">

                        <label>Ngôn ngữ *</label>

                        <input
                            type="text"
                            name="language"
                            value="{{ old('language', 'vi') }}"
                            placeholder="Ví dụ: vi, en, ko"
                            maxlength="10"
                            required
                        >

                        @error('language')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Label --}}

                    <div class="field">

                        <label>Label *</label>

                        <input
                            type="text"
                            name="label"
                            value="{{ old('label', 'Vietsub') }}"
                            placeholder="Ví dụ: Vietsub"
                            maxlength="100"
                            required
                        >

                        @error('label')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Link subtitle --}}

                    <div class="field full">

                        <label>URL subtitle *</label>

                        <input
                            type="url"
                            name="file_url"
                            value="{{ old('file_url') }}"
                            placeholder="https://cdn.example.com/subtitles/episode-1.vtt"
                            maxlength="500"
                            required
                        >

                        @error('file_url')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                        <small>
                            Nhập link file subtitle .srt hoặc .vtt
                            được lưu trên Cloud.
                        </small>

                    </div>


                    {{-- Định dạng --}}

                    <div class="field">

                        <label>Định dạng *</label>

                        <select
                            name="format"
                            required
                        >

                            <option value="">
                                Chọn định dạng
                            </option>

                            <option
                                value="vtt"
                                {{ old('format') === 'vtt' ? 'selected' : '' }}
                            >
                                VTT (.vtt)
                            </option>

                            <option
                                value="srt"
                                {{ old('format') === 'srt' ? 'selected' : '' }}
                            >
                                SRT (.srt)
                            </option>

                        </select>

                        @error('format')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Mặc định --}}

                    <div class="setting-row field">

                        <div class="st-txt">

                            <strong>Subtitle mặc định</strong>

                            <small>
                                Sử dụng subtitle này mặc định khi xem phim.
                            </small>

                        </div>

                        <label class="toggle">

                            <input
                                type="checkbox"
                                name="is_default"
                                value="1"
                                {{ old('is_default') ? 'checked' : '' }}
                            >

                            <span class="track"></span>

                        </label>

                    </div>


                    {{-- Trạng thái --}}

                    <div class="setting-row field full">

                        <div class="st-txt">

                            <strong>Trạng thái subtitle</strong>

                            <small>
                                Cho phép subtitle này được sử dụng.
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
                            href="{{ route('admin.subtitles') }}"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Lưu subtitle
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</section>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const movieSelect = document.getElementById('movie_id');
    const episodeSelect = document.getElementById('episode_id');

    function filterEpisodes() {

        const movieId = movieSelect.value;

        Array.from(episodeSelect.options).forEach(function (option) {

            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.movie !== movieId;

        });

        // Nếu episode hiện tại không thuộc movie
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

    // Lọc episode ngay khi load trang
    filterEpisodes();

});

</script>

@endsection