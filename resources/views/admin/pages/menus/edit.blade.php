@extends('admin.layouts.master')

@section('content')

<section id="menus-edit" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa menu</h3>
            <p>Chỉnh sửa thông tin menu {{ $menu->name }}.</p>
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
                action="{{ route('admin.menus.update', $menu) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                {{-- Tên --}}
                <div class="field">

                    <label for="name">
                        Tên menu
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $menu->name) }}"
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
                        value="{{ old('url', $menu->url) }}"
                        placeholder="/phim-le"
                    >

                    @error('url')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Location --}}
                <div class="field">

                    <label for="location">
                        Vị trí menu
                    </label>

                    <select
                        id="location"
                        name="location"
                        required
                    >

                        <option
                            value="main"
                            {{ old('location', $menu->location) === 'main' ? 'selected' : '' }}
                        >
                            Main menu
                        </option>

                        <option
                            value="footer"
                            {{ old('location', $menu->location) === 'footer' ? 'selected' : '' }}
                        >
                            Footer menu
                        </option>

                        <option
                            value="mobile"
                            {{ old('location', $menu->location) === 'mobile' ? 'selected' : '' }}
                        >
                            Mobile menu
                        </option>

                    </select>

                    @error('location')
                        <small style="color:#c00;">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Parent --}}
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
                                {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}
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
                        value="{{ old('icon', $menu->icon) }}"
                        placeholder="Ví dụ: home"
                    >

                </div>


                {{-- Sort --}}
                <div class="field">

                    <label for="sort_order">
                        Thứ tự
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', $menu->sort_order) }}"
                        min="0"
                    >

                </div>


                {{-- Status --}}
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
                            {{ old('is_active', $menu->is_active) ? 'checked' : '' }}
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
                        Lưu thay đổi
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