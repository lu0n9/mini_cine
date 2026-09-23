@extends('admin.layouts.master')

@section('content')

<section id="add-season" class="page">

    {{-- ================= HEADER ================= --}}
    <div class="page-head">
        <div>
            <h3>Thêm season</h3>
            <p>Tạo mùa phim mới và thiết lập thông tin hiển thị.</p>
        </div>

        <a class="btn ghost" href="{{ route('admin.seasons') }}">
            ← Quay lại seasons
        </a>
    </div>


    {{-- ================= VALIDATION ================= --}}
    @if ($errors->any())
        <div
            class="alert alert-danger"
            style="margin-bottom:18px;"
        >
            <strong>Có lỗi xảy ra, vui lòng kiểm tra lại:</strong>

            <ul style="margin:8px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ================= FORM ================= --}}
    <form
        action="{{ route('admin.seasons.store') }}"
        method="POST"
    >

        @csrf


        {{-- ================= THÔNG TIN SEASON ================= --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Thông tin season</h4>
            </div>

            <div class="panel-body">

                <div class="form-grid">

                    {{-- PHIM --}}
                    <div class="field">

                        <label>
                            Phim <span style="color:#e53935;">*</span>
                        </label>

                        <div class="select-wrap">

                            <select
                                name="movie_id"
                                class="admin-select"
                            >

                                <option value="">
                                    Chọn phim...
                                </option>

                                @foreach ($movies as $movie)

                                    <option
                                        value="{{ $movie->id }}"
                                        {{ old('movie_id') == $movie->id ? 'selected' : '' }}
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
                            Tên season <span style="color:#e53935;">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
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
                            Số thứ tự <span style="color:#e53935;">*</span>
                        </label>

                        <input
                            type="number"
                            name="season_number"
                            value="{{ old('season_number', 1) }}"
                            min="1"
                            placeholder="1"
                        >

                        <small
                            style="
                                display:block;
                                margin-top:6px;
                                color:#888;
                                font-size:12px;
                            "
                        >
                            Thứ tự của season trong phim.
                        </small>

                        @error('season_number')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- NGÀY PHÁT HÀNH --}}
                    <div class="field">

                        <label>Ngày phát hành</label>

                        <input
                            type="date"
                            name="release_date"
                            value="{{ old('release_date') }}"
                        >

                        @error('release_date')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- MÔ TẢ --}}
                    <div class="field full">

                        <label>Mô tả season</label>

                        <textarea
                            name="description"
                            placeholder="Tóm tắt nội dung của season..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- ================= HIỂN THỊ ================= --}}
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
                                Cho phép người xem truy cập season này.
                            </small>

                        </div>


                        <label class="toggle">

                            <input
                                type="checkbox"
                                name="is_published"
                                value="1"
                                {{ old('is_published', 1) ? 'checked' : '' }}
                            >

                            <span class="track"></span>

                        </label>

                    </div>


                    {{-- ================= ACTION ================= --}}
                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.seasons') }}"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Lưu season
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</section>


{{-- ================= SELECT STYLE ================= --}}
<style>

.select-wrap {
    position: relative;
    width: 100%;
}

.admin-select {
    width: 100%;
    height: 44px;

    padding: 0 42px 0 13px;

    border: 1px solid #d9d9d9;
    border-radius: 8px;

    background-color: #fff;

    color: #222;

    font-size: 14px;
    font-weight: 500;

    outline: none;

    cursor: pointer;

    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    transition:
        border-color .2s ease,
        box-shadow .2s ease,
        background-color .2s ease;
}

.admin-select:hover {
    border-color: #b8b8b8;
    background-color: #fafafa;
}

.admin-select:focus {
    border-color: #222;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, .06);
    background-color: #fff;
}

.admin-select option {
    color: #222;
    background: #fff;
    font-size: 14px;
}

.select-arrow {
    position: absolute;

    top: 50%;
    right: 13px;

    transform: translateY(-50%);

    display: flex;
    align-items: center;
    justify-content: center;

    color: #222;

    pointer-events: none;
}

</style>

@endsection