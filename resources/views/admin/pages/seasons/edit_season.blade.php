@extends('admin.layouts.master')

@section('content')

<section id="edit-season" class="page">

    {{-- HEADER --}}
    <div class="page-head">

        <div>
            <h3>Sửa season</h3>

            <p>
                Chỉnh sửa thông tin và trạng thái season.
            </p>
        </div>

        <a
            href="{{ route('admin.seasons') }}"
            class="btn ghost"
        >
            ← Quay lại seasons
        </a>

    </div>


    {{-- VALIDATION --}}
    @if ($errors->any())

        <div
            class="alert alert-danger"
            style="margin-bottom:18px;"
        >

            <strong>
                Có lỗi xảy ra, vui lòng kiểm tra lại:
            </strong>

            <ul style="margin:8px 0 0 18px;">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORM --}}
    <form
        action="{{ route('admin.seasons.update', $season->id) }}"
        method="POST"
    >

        @csrf

        @method('PUT')


        <div class="panel">

            <div class="panel-head">
                <h4>Thông tin season</h4>
            </div>


            <div class="panel-body">

                <div class="form-grid">


                    {{-- PHIM --}}
                    <div class="field">

                        <label>
                            Phim
                            <span style="color:#e53935;">
                                *
                            </span>
                        </label>

                        <div class="select-wrap">

                            <select
                                name="movie_id"
                                class="admin-select"
                            >

                                <option value="">
                                    Chọn phim...
                                </option>

                                @foreach($movies as $movie)

                                    <option
                                        value="{{ $movie->id }}"
                                        {{ old('movie_id', $season->movie_id) == $movie->id ? 'selected' : '' }}
                                    >
                                        {{ $movie->title }}
                                    </option>

                                @endforeach

                            </select>

                            <span class="select-arrow">

                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>

                            </span>

                        </div>

                        @error('movie_id')

                            <small class="text-danger">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- TÊN SEASON --}}
                    <div class="field">

                        <label>
                            Tên season
                            <span style="color:#e53935;">
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $season->name) }}"
                            placeholder="Ví dụ: Season 1"
                        >

                        @error('name')

                            <small class="text-danger">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- SỐ THỨ TỰ --}}
                    <div class="field">

                        <label>
                            Số thứ tự
                            <span style="color:#e53935;">
                                *
                            </span>
                        </label>

                        <input
                            type="number"
                            name="season_number"
                            value="{{ old('season_number', $season->season_number) }}"
                            min="1"
                        >

                        @error('season_number')

                            <small class="text-danger">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- NGÀY PHÁT HÀNH --}}
                    <div class="field">

                        <label>
                            Ngày phát hành
                        </label>

                        <input
                            type="date"
                            name="release_date"
                            value="{{ old(
                                'release_date',
                                $season->release_date
                                    ? $season->release_date->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                        @error('release_date')

                            <small class="text-danger">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- MÔ TẢ --}}
                    <div class="field full">

                        <label>
                            Mô tả season
                        </label>

                        <textarea
                            name="description"
                            placeholder="Tóm tắt nội dung của season..."
                        >{{ old('description', $season->description) }}</textarea>

                        @error('description')

                            <small class="text-danger">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- HIỂN THỊ --}}
                    <div
                        class="setting-row field full"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:20px;
                        "
                    >

                        <div class="st-txt">

                            <strong>
                                Hiển thị season
                            </strong>

                            <small>
                                Cho phép người xem truy cập
                                season này.
                            </small>

                        </div>


                        <label class="toggle">

                            <input
                                type="checkbox"
                                name="is_published"
                                value="1"
                                {{ old(
                                    'is_published',
                                    $season->is_published
                                ) ? 'checked' : '' }}
                            >

                            <span class="track"></span>

                        </label>

                    </div>


                    {{-- BUTTON --}}
                    <div class="form-actions">

                        <a
                            href="{{ route('admin.seasons') }}"
                            class="btn ghost"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Lưu thay đổi
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</section>

@endsection