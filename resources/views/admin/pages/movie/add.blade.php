@extends('admin.layouts.master')

@section('content')

<section id="add" class="page">

    <div class="page-head">
        <div>
            <h3>Thêm phim mới</h3>
            <p>Điền đầy đủ thông tin, media và SEO cho phim.</p>
        </div>
    </div>

    {{-- FORM THÊM PHIM --}}
    <form
        action="{{ route('admin.movies.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:18px;">
                <strong>Có lỗi xảy ra, vui lòng kiểm tra lại:</strong>
                <ul style="margin:8px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ================= THÔNG TIN CƠ BẢN ================= --}}
        <div class="panel">
            <div class="panel-head">
                <h4>Thông tin cơ bản</h4>
            </div>

            <div class="panel-body">

                <div class="form-grid">

                    <div class="field">
                        <label>Tên phim</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="VD: Last Horizon"
                        >
                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Tên gốc</label>
                        <input
                            name="original_title"
                            value="{{ old('original_title') }}"
                            placeholder="Original title"
                        >
                    </div>

                    <div class="field">
                        <label>Slug</label>
                        <input
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="last-horizon"
                        >
                        @error('slug')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Loại phim</label>
                        <select name="type">
                            @foreach($types as $type)
                                <option
                                    value="{{ $type }}"
                                    {{ old('type', 'single') === $type ? 'selected' : '' }}
                                >
                                    {{ $type === 'single' ? 'Phim lẻ' : 'Phim bộ' }}
                                </option>
                            @endforeach
                        </select>

                        @error('type') <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Năm phát hành</label>
                        <input
                            type="number"
                            name="release_year"
                            value="{{ old('release_year') }}"
                            placeholder="2024"
                        >
                        @error('release_year')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Ngày phát hành</label>
                        <input
                            type="date"
                            name="release_date"
                            value="{{ old('release_date') }}"
                        >
                        @error('release_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Thời lượng (phút)</label>
                        <input
                            type="number"
                            name="duration"
                            value="{{ old('duration') }}"
                            placeholder="128"
                        >
                        @error('duration')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Chất lượng</label>
                        <select name="quality">
                            @foreach($qualities as $quality)
                                <option
                                    value="{{ $quality }}"
                                    {{ old('quality', 'HD') === $quality ? 'selected' : '' }}
                                >
                                    {{ $quality }}
                                </option>
                            @endforeach
                        </select>

                        @error('quality') <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="field">
                        <label>Ngôn ngữ</label>
                        <select name="language">
                            @foreach($languages as $language)
                                <option
                                    value="{{ $language }}"
                                    {{ old('language', 'vietsub') === $language ? 'selected' : '' }}
                                >
                                    @switch($language)
                                        @case('vietsub')
                                            Vietsub
                                            @break

                                    @case('thuyet_minh')
                                        Thuyết minh
                                        @break

                                    @case('long_tieng')
                                        Lồng tiếng
                                        @break

                                    @default
                                        {{ $language }}
                                @endswitch
                            </option>
                        @endforeach
                        </select>

                        @error('language') <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    {{-- QUỐC GIA --}}
                    <div class="field">
                        <label>Quốc gia</label>

                        <div class="mini-picker" data-picker="countries" data-multiple="true">
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>
                                <input type="text" class="mini-picker-search" placeholder="Chọn quốc gia...">
                            </div>

                            <div class="mini-picker-menu">
                                @foreach($countries as $country)
                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $country->id }}"
                                        data-label="{{ $country->name }}"
                                    >
                                        {{ $country->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mini-picker-hidden"></div>
                        @error('countries.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>IMDb ID</label>
                        <input
                            name="imdb_id"
                            value="{{ old('imdb_id') }}"
                            placeholder="tt0000000"
                        >
                    </div>

                    <div class="field">
                        <label>IMDb Rating</label>
                        <input
                            type="number"
                            step="0.1"
                            min="0"
                            max="10"
                            name="imdb_rating"
                            value="{{ old('imdb_rating') }}"
                            placeholder="8.4"
                        >
                    </div>

                    <div class="field">
                        <label>TMDB ID</label>
                        <input
                            type="number"
                            name="tmdb_id"
                            value="{{ old('tmdb_id') }}"
                            placeholder="123456"
                        >
                    </div>

                    {{-- THỂ LOẠI --}}
                    <div class="field full">
                        <label>Thể loại</label>

                        <div class="mini-picker" data-picker="genres" data-multiple="true">
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>
                                <input type="text" class="mini-picker-search" placeholder="Chọn thể loại...">
                            </div>

                            <div class="mini-picker-menu">
                                @foreach($genres as $genre)
                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $genre->id }}"
                                        data-label="{{ $genre->name }}"
                                    >
                                        {{ $genre->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mini-picker-hidden"></div>
                        @error('genres.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>
                    {{-- COLLECTION --}}
                    <div class="field full">
                        <label>Collection</label>

                        <div
                            class="mini-picker"
                            data-picker="collections"
                            data-multiple="true"
                        >
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>

                                <input
                                    type="text"
                                    class="mini-picker-search"
                                    placeholder="Chọn Collection..."
                                >
                            </div>

                            <div class="mini-picker-menu">

                                @foreach($collections as $collection)

                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $collection->id }}"
                                        data-label="{{ $collection->name }}"
                                    >
                                        {{ $collection->name }}
                                    </button>

                                @endforeach

                            </div>

                            <div class="mini-picker-hidden"></div>

                        </div>

                        @error('collections.*')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                    {{-- TAGS --}}
                    <div class="field full">
                        <label>Tags</label>

                        <div
                            class="mini-picker"
                            data-picker="tags"
                            data-multiple="true"
                        >
                            <div class="mini-picker-input">

                                <div class="mini-picker-tags"></div>

                                <input
                                    type="text"
                                    class="mini-picker-search"
                                    placeholder="Chọn tags..."
                                >

                            </div>

                            <div class="mini-picker-menu">

                                @foreach($tags as $tag)

                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $tag->id }}"
                                        data-label="{{ $tag->name }}"
                                    >
                                        {{ $tag->name }}
                                    </button>

                                @endforeach

                            </div>

                            <div class="mini-picker-hidden"></div>

                        </div>

                        @error('tags.*')
                            <small class="text-danger">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                    <div class="field">
                        <label>Đạo diễn</label>

                        <div class="mini-picker" data-picker="director" data-multiple="false">
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>
                                <input type="text" class="mini-picker-search" placeholder="Chọn đạo diễn...">
                            </div>

                            <div class="mini-picker-menu">
                                @foreach($people as $person)
                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $person->id }}"
                                        data-label="{{ $person->name }}"
                                    >
                                        {{ $person->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mini-picker-hidden"></div>
                        @error('director')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>Nhà sản xuất</label>

                        <div class="mini-picker" data-picker="producer" data-multiple="false">
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>
                                <input type="text" class="mini-picker-search" placeholder="Chọn nhà sản xuất...">
                            </div>

                            <div class="mini-picker-menu">
                                @foreach($people as $person)
                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $person->id }}"
                                        data-label="{{ $person->name }}"
                                    >
                                        {{ $person->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mini-picker-hidden"></div>
                        @error('producer')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>

                    <div class="field full">
                        <label>Diễn viên</label>

                        <div class="mini-picker" data-picker="actors" data-multiple="true">
                            <div class="mini-picker-input">
                                <div class="mini-picker-tags"></div>
                                <input type="text" class="mini-picker-search" placeholder="Chọn diễn viên...">
                            </div>

                            <div class="mini-picker-menu">
                                @foreach($people as $person)
                                    <button
                                        type="button"
                                        class="mini-picker-option"
                                        data-value="{{ $person->id }}"
                                        data-label="{{ $person->name }}"
                                    >
                                        {{ $person->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="mini-picker-hidden"></div>
                        @error('actors.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>

                    <div class="field full">
                        <label>Mô tả ngắn</label>

                        <textarea
                            name="short_description"
                            placeholder="Tóm tắt nội dung..."
                        >{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field full">
                        <label>Mô tả đầy đủ</label>

                        <textarea
                            name="description"
                            placeholder="Nội dung chi tiết..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Trailer URL</label>

                        <input
                            name="trailer_url"
                            value="{{ old('trailer_url') }}"
                            placeholder="https://youtube.com/..."
                        >
                    </div>

                    <div class="field">
                        <label>Trạng thái</label>

                        <select name="status">
                            @foreach($statuses as $status)
                                <option
                                    value="{{ $status }}"
                                    {{ old('status', 'completed') === $status ? 'selected' : '' }}
                                >
                                    @switch($status)
                                        @case('draft')
                                            Nháp
                                            @break
                                    @case('ongoing')
                                        Đang chiếu
                                        @break

                                    @case('completed')
                                        Hoàn thành
                                        @break

                                    @default
                                        {{ $status }}
                                @endswitch
                            </option>
                        @endforeach


                        </select>

                        @error('status') <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

            </div>
        </div>


        {{-- ================= MEDIA ================= --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Media</h4>
            </div>

            <div class="panel-body">

                <div class="form-grid three">

                    <div class="upload">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>

                        <p style="margin-top:8px">
                            <b>Poster</b> (2:3)
                        </p>

                        <input
                            type="file"
                            name="poster"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </div>


                    <div class="upload">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>

                        <p style="margin-top:8px">
                            <b>Thumbnail</b>
                        </p>

                        <input
                            type="file"
                            name="thumbnail"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </div>


                    <div class="upload">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>

                        <p style="margin-top:8px">
                            <b>Banner / Backdrop</b>
                        </p>

                        <input
                            type="file"
                            name="backdrop"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </div>

                </div>


                <div style="display:flex;gap:24px;margin-top:18px;flex-wrap:wrap">

                    <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600">
                        <span class="toggle">
                            <input
                                type="checkbox"
                                name="featured"
                                value="1"
                                {{ old('featured') ? 'checked' : '' }}
                            >
                            <span class="track"></span>
                        </span>
                        Featured
                    </label>


                    <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600">
                        <span class="toggle">
                            <input
                                type="checkbox"
                                name="popular"
                                value="1"
                                {{ old('popular') ? 'checked' : '' }}
                            >
                            <span class="track"></span>
                        </span>
                        Popular
                    </label>


                    <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600">
                        <span class="toggle">
                            <input
                                type="checkbox"
                                name="recommended"
                                value="1"
                                {{ old('recommended') ? 'checked' : '' }}
                            >
                            <span class="track"></span>
                        </span>
                        Recommended
                    </label>

                </div>

            </div>
        </div>


        {{-- ================= SEO ================= --}}
        <div class="panel">

            <div class="panel-head">
                <h4>SEO</h4>
            </div>

            <div class="panel-body">

                <div class="form-grid">

                    <div class="field">
                        <label>SEO Title</label>

                        <input
                            name="seo_title"
                            value="{{ old('seo_title') }}"
                        >
                    </div>


                    <div class="field">
                        <label>Canonical URL</label>

                        <input
                            name="canonical_url"
                            value="{{ old('canonical_url') }}"
                            placeholder="https://..."
                        >
                    </div>


                    <div class="field full">
                        <label>SEO Description</label>

                        <textarea
                            name="seo_description"
                            style="min-height:70px"
                        >{{ old('seo_description') }}</textarea>
                        @error('seo_description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="field full">
                        <label>SEO Keywords</label>

                        <input
                            name="seo_keywords"
                            value="{{ old('seo_keywords') }}"
                            placeholder="từ khóa 1, từ khóa 2"
                        >
                    </div>


                    <div class="field">
                        <label>OG Title</label>

                        <input
                            name="og_title"
                            value="{{ old('og_title') }}"
                        >
                    </div>


                    <div class="field">
                        <label>OG Image URL</label>

                        <input
                            name="og_image"
                            value="{{ old('og_image') }}"
                        >
                    </div>


                    <div class="field full">
                        <label>OG Description</label>

                        <textarea
                            name="og_description"
                            style="min-height:70px"
                        >{{ old('og_description') }}</textarea>
                        @error('og_description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="form-actions">

                        <button
                            class="btn ghost"
                            type="submit"
                            name="save_type"
                            value="draft"
                        >
                            Lưu nháp
                        </button>

                        <button
                            class="btn"
                            type="submit"
                            name="save_type"
                            value="publish"
                        >
                            Xuất bản phim
                        </button>

                    </div>

                </div>

            </div>
        </div>

    </form>

</section>




<script>
document.addEventListener('DOMContentLoaded', function () {
    const oldValues = {
        countries: @json(old('countries', [])),
        genres: @json(old('genres', [])),
        collections: @json(old('collections', [])),
        tags: @json(old('tags', [])),
        director: @json(old('director')),
        producer: @json(old('producer')),
        actors: @json(old('actors', []))
    };

    document.querySelectorAll('.mini-picker').forEach(function (picker) {
        const name = picker.dataset.picker;
        const multiple = picker.dataset.multiple === 'true';
        const searchInput = picker.querySelector('.mini-picker-search');
        const tags = picker.querySelector('.mini-picker-tags');
        const menu = picker.querySelector('.mini-picker-menu');
        const hidden = picker.querySelector('.mini-picker-hidden');
        const options = Array.from(picker.querySelectorAll('.mini-picker-option'));

        let selected = [];

        if (multiple) {
            const values = Array.isArray(oldValues[name]) ? oldValues[name] : [];
            selected = values.map(String);
        } else if (oldValues[name] !== null && oldValues[name] !== undefined && oldValues[name] !== '') {
            selected = [String(oldValues[name])];
        }

        function getOption(value) {
            return options.find(function (option) {
                return String(option.dataset.value) === String(value);
            });
        }

        function render() {
            tags.innerHTML = '';
            hidden.innerHTML = '';

            selected.forEach(function (value) {
                const option = getOption(value);

                if (!option) {
                    return;
                }

                const tag = document.createElement('span');
                tag.className = 'mini-picker-tag';

                const label = document.createElement('span');
                label.textContent = option.dataset.label;

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'mini-picker-tag-remove';
                remove.setAttribute('aria-label', 'Xóa ' + option.dataset.label);
                remove.textContent = '×';

                remove.addEventListener('click', function (event) {
                    event.stopPropagation();
                    selected = selected.filter(function (item) {
                        return String(item) !== String(value);
                    });
                    render();
                });

                tag.appendChild(label);
                tag.appendChild(remove);
                tags.appendChild(tag);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = multiple ? name + '[]' : name;
                input.value = value;
                hidden.appendChild(input);
            });

            options.forEach(function (option) {
                const isSelected = selected.includes(String(option.dataset.value));
                option.classList.toggle('selected', isSelected);
            });
        }

        function filterOptions() {
            const keyword = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            options.forEach(function (option) {
                const label = option.dataset.label.toLowerCase();
                const match = label.includes(keyword);
                option.style.display = match ? 'block' : 'none';

                if (match) {
                    visibleCount++;
                }
            });

            let empty = menu.querySelector('.mini-picker-empty');

            if (visibleCount === 0) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.className = 'mini-picker-empty';
                    empty.textContent = 'Không tìm thấy kết quả';
                    menu.appendChild(empty);
                }
            } else if (empty) {
                empty.remove();
            }
        }

        function openPicker() {
            document.querySelectorAll('.mini-picker.open').forEach(function (other) {
                if (other !== picker) {
                    other.classList.remove('open');
                }
            });

            picker.classList.add('open');
            filterOptions();
        }

        searchInput.addEventListener('focus', openPicker);

        picker.querySelector('.mini-picker-input').addEventListener('click', function () {
            openPicker();
            searchInput.focus();
        });

        searchInput.addEventListener('input', filterOptions);

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                const value = String(option.dataset.value);

                if (multiple) {
                    if (selected.includes(value)) {
                        selected = selected.filter(function (item) {
                            return String(item) !== value;
                        });
                    } else {
                        selected.push(value);
                    }
                } else {
                    selected = [value];
                    picker.classList.remove('open');
                }

                searchInput.value = '';
                render();
                filterOptions();

                if (!multiple) {
                    searchInput.blur();
                }
            });
        });

        render();
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.mini-picker')) {
            document.querySelectorAll('.mini-picker.open').forEach(function (picker) {
                picker.classList.remove('open');
            });
        }
    });
});
</script>

@endsection