@extends('admin.layouts.master')

@section('content')

<section id="edit-episode" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa episode</h3>
            <p>Cập nhật thông tin tập phim, lịch phát hành và quyền truy cập.</p>
        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.episodes') }}"
        >
            ← Quay lại episodes
        </a>

    </div>


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.episodes.update', $episode->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="form-grid three">


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
                                    {{ old('movie_id', $episode->movie_id) == $movie->id ? 'selected' : '' }}
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


                    {{-- Season --}}
                    <div class="field">

                        <label>Season *</label>

                        <select
                            name="season_id"
                            id="season_id"
                            required
                        >

                            <option value="">
                                Chọn season
                            </option>

                            @foreach($seasons as $season)

                                <option
                                    value="{{ $season->id }}"
                                    data-movie="{{ $season->movie_id }}"
                                    {{ old('season_id', $episode->season_id) == $season->id ? 'selected' : '' }}
                                >
                                    {{ $season->movie->title ?? '—' }}
                                    ·
                                    {{ $season->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('season_id')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Số tập --}}
                    <div class="field">

                        <label>Số tập *</label>

                        <input
                            type="number"
                            name="episode_number"
                            value="{{ old('episode_number', $episode->episode_number) }}"
                            min="1"
                            placeholder="Ví dụ: 1"
                            required
                        >

                        @error('episode_number')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Tiêu đề --}}
                    <div class="field">

                        <label>Tiêu đề tập</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $episode->name) }}"
                            placeholder="Tên episode"
                        >

                        @error('name')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Thời lượng --}}
                    <div class="field">

                        <label>Thời lượng</label>

                        <input
                            type="number"
                            name="duration"
                            value="{{ old('duration', $episode->duration) }}"
                            min="1"
                            placeholder="Thời lượng (phút)"
                        >

                        @error('duration')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Ngày phát hành --}}
                    <div class="field">

                        <label>Ngày phát hành</label>

                        <input
                            type="date"
                            name="release_date"
                            value="{{ old(
                                'release_date',
                                $episode->release_date
                                    ? $episode->release_date->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                        @error('release_date')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Mô tả --}}
                    <div class="field full">

                        <label>Mô tả tập</label>

                        <textarea
                            name="description"
                            placeholder="Nội dung tóm tắt episode..."
                        >{{ old('description', $episode->description) }}</textarea>

                        @error('description')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Nhãn --}}
                    <div class="field">

                        <label>Nhãn</label>

                        <select name="label">

                            <option
                                value=""
                                {{ old('label', $episode->label) == '' ? 'selected' : '' }}
                            >
                                Không có
                            </option>

                            <option
                                value="new"
                                {{ old('label', $episode->label) == 'new' ? 'selected' : '' }}
                            >
                                Mới
                            </option>

                            <option
                                value="vip"
                                {{ old('label', $episode->label) == 'vip' ? 'selected' : '' }}
                            >
                                VIP
                            </option>

                        </select>

                        @error('label')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Trạng thái --}}
                    <div class="field">

                        <label>Trạng thái</label>

                        <select name="is_active">

                            <option
                                value="1"
                                {{ old('is_active', $episode->is_active ? '1' : '0') == '1' ? 'selected' : '' }}
                            >
                                Hiển thị
                            </option>

                            <option
                                value="0"
                                {{ old('is_active', $episode->is_active ? '1' : '0') == '0' ? 'selected' : '' }}
                            >
                                Ẩn
                            </option>

                        </select>

                        @error('is_active')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Thứ tự --}}
                    <div class="field">

                        <label>Thứ tự</label>

                        <input
                            type="number"
                            name="sort_order"
                            value="{{ old('sort_order', $episode->sort_order) }}"
                            min="1"
                            placeholder="Ví dụ: 1"
                        >

                        @error('sort_order')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Buttons --}}
                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.episodes') }}"
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

            </form>

        </div>

    </div>

</section>

@endsection