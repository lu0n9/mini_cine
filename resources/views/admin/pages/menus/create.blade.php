@extends('admin.layouts.master')

@section('content')

<section id="menus-create" class="page">

    <div class="page-head">

        <div>
            <h3>Thêm menu</h3>
            <p>Tạo menu mới cho website Mini Cine.</p>
        </div>

        <a
            href="{{ route('admin.menus.index') }}"
            class="btn"
        >
            ← Quay lại
        </a>

    </div>


    <div class="panel">

        <div class="panel-head">
            <h4>Thông tin menu</h4>
        </div>

        <div class="panel-body">

            <form
                action="{{ route('admin.menus.store') }}"
                method="POST"
            >

                @csrf


                {{-- Tên --}}
                <div class="field">

                    <label for="name">
                        Tên menu
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ví dụ: Phim lẻ"
                        required
                    >

                    @error('name')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- URL --}}
                <div class="field">

                    <label for="url">
                        URL
                    </label>

                    <input
                        type="text"
                        id="url"
                        name="url"
                        value="{{ old('url') }}"
                        placeholder="Ví dụ: /phim-le"
                    >

                    @error('url')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Vị trí --}}
                <div class="field">

                    <label for="location">
                        Vị trí menu
                    </label>

                    <select
                        id="location"
                        name="location"
                        required
                    >

                        <option value="main"
                            {{ old('location', 'main') === 'main' ? 'selected' : '' }}>
                            Main menu
                        </option>

                        <option value="footer"
                            {{ old('location') === 'footer' ? 'selected' : '' }}>
                            Footer menu
                        </option>

                        <option value="mobile"
                            {{ old('location') === 'mobile' ? 'selected' : '' }}>
                            Mobile menu
                        </option>

                    </select>

                    @error('location')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Menu cha --}}
                <div class="field">

                    <label for="parent_id">
                        Menu cha
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                    >

                        <option value="">
                            — Không có menu cha —
                        </option>

                        @foreach($parents as $parent)

                            <option
                                value="{{ $parent->id }}"
                                data-location="{{ $parent->location }}"
                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}
                            >
                                {{ $parent->name }}
                                —
                                {{ ucfirst($parent->location) }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Chọn menu cha nếu đây là menu con.
                    </small>

                    @error('parent_id')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Icon --}}
                <div class="field">

                    <label for="icon">
                        Icon
                    </label>

                    <input
                        type="text"
                        id="icon"
                        name="icon"
                        value="{{ old('icon') }}"
                        placeholder="Ví dụ: home"
                    >

                    <small>
                        Có thể để trống.
                    </small>

                </div>


                {{-- Thứ tự --}}
                <div class="field">

                    <label for="sort_order">
                        Thứ tự
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                    >

                </div>


                {{-- Trạng thái --}}
                <div class="field">

                    <label>
                        Trạng thái
                    </label>

                    <label style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                        cursor:pointer;
                    ">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >

                        <span>
                            Hiển thị menu
                        </span>

                    </label>

                </div>


                {{-- Buttons --}}
                <div style="
                    display:flex;
                    gap:10px;
                    margin-top:20px;
                ">

                    <button
                        type="submit"
                        class="btn"
                    >
                        Lưu menu
                    </button>

                    <a
                        href="{{ route('admin.menus.index') }}"
                        class="btn"
                    >
                        Hủy
                    </a>

                </div>

            </form>

        </div>

    </div>

</section>


{{-- Chỉ hiển thị menu cha cùng location --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const locationSelect = document.getElementById('location');
    const parentSelect = document.getElementById('parent_id');

    function filterParents() {

        const location = locationSelect.value;

        Array.from(parentSelect.options).forEach(function (option) {

            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                option.dataset.location !== location;

        });

        const selected = parentSelect.options[parentSelect.selectedIndex];

        if (
            selected &&
            selected.value &&
            selected.dataset.location !== location
        ) {
            parentSelect.value = '';
        }
    }

    locationSelect.addEventListener('change', filterParents);

    filterParents();

});
</script>

@endsection