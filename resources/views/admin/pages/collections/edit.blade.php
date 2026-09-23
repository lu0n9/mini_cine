@extends('admin.layouts.master')

@section('content')

<section id="collections-edit" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa Collection</h3>

            <p>
                Chỉnh sửa Collection:
                <strong>{{ $collection->name }}</strong>
            </p>
        </div>

        <a
            href="{{ route('admin.collections') }}"
            class="btn ghost"
        >
            ← Quay lại
        </a>

    </div>


    {{-- Validation --}}
    @if($errors->any())

        <div class="alert error">

            <ul style="margin:0; padding-left:20px;">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.collections.update', $collection) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                @method('PUT')


                {{-- Tên --}}
                <div class="field">

                    <label for="name">
                        Tên Collection
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $collection->name) }}"
                        required
                    >

                </div>


                {{-- Slug --}}
                <div class="field">

                    <label for="slug">
                        Slug
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $collection->slug) }}"
                    >

                </div>


                {{-- Description --}}
                <div class="field">

                    <label for="description">
                        Mô tả
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Mô tả về Collection..."
                    >{{ old('description', $collection->description) }}</textarea>

                </div>


                {{-- POSTER --}}
                <div class="field">

                    <label for="poster">
                        Poster Collection
                    </label>


                    {{-- Ảnh hiện tại --}}
                    @if($collection->poster)

                        <div
                            id="currentPosterWrapper"
                            style="margin-bottom:15px;"
                        >

                            <p class="hint">
                                Poster hiện tại
                            </p>

                            <img
                                id="currentPoster"
                                src="{{ asset('storage/' . $collection->poster) }}"
                                alt="{{ $collection->name }}"
                                style="
                                    display:block;
                                    width:180px;
                                    height:260px;
                                    object-fit:cover;
                                    border-radius:10px;
                                    border:1px solid rgba(255,255,255,.12);
                                "
                            >

                            <label
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:8px;
                                    margin-top:10px;
                                "
                            >

                                <input
                                    type="checkbox"
                                    name="remove_poster"
                                    value="1"
                                >

                                <span>
                                    Xóa Poster hiện tại
                                </span>

                            </label>

                        </div>

                    @endif


                    {{-- Upload ảnh mới --}}
                    <input
                        type="file"
                        id="poster"
                        name="poster"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <p class="hint">
                        Chọn ảnh mới nếu muốn thay Poster hiện tại.
                        JPG, PNG, WEBP · tối đa 5MB.
                    </p>


                    {{-- Preview ảnh mới --}}
                    <div
                        id="posterPreviewWrapper"
                        style="display:none; margin-top:15px;"
                    >

                        <p class="hint">
                            Xem trước Poster mới
                        </p>

                        <img
                            id="posterPreview"
                            src=""
                            alt="Poster preview"
                            style="
                                display:block;
                                width:180px;
                                height:260px;
                                object-fit:cover;
                                border-radius:10px;
                                border:1px solid rgba(255,255,255,.12);
                            "
                        >

                    </div>

                </div>


                {{-- BACKDROP --}}
                <div class="field">

                    <label for="backdrop">
                        Backdrop Collection
                    </label>


                    {{-- Ảnh hiện tại --}}
                    @if($collection->backdrop)

                        <div
                            id="currentBackdropWrapper"
                            style="margin-bottom:15px;"
                        >

                            <p class="hint">
                                Backdrop hiện tại
                            </p>

                            <img
                                id="currentBackdrop"
                                src="{{ asset('storage/' . $collection->backdrop) }}"
                                alt="{{ $collection->name }}"
                                style="
                                    display:block;
                                    width:100%;
                                    max-width:700px;
                                    height:260px;
                                    object-fit:cover;
                                    border-radius:10px;
                                    border:1px solid rgba(255,255,255,.12);
                                "
                            >

                            <label
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:8px;
                                    margin-top:10px;
                                "
                            >

                                <input
                                    type="checkbox"
                                    name="remove_backdrop"
                                    value="1"
                                >

                                <span>
                                    Xóa Backdrop hiện tại
                                </span>

                            </label>

                        </div>

                    @endif


                    {{-- Upload ảnh mới --}}
                    <input
                        type="file"
                        id="backdrop"
                        name="backdrop"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <p class="hint">
                        Chọn ảnh mới nếu muốn thay Backdrop hiện tại.
                        JPG, PNG, WEBP · tối đa 10MB.
                    </p>


                    {{-- Preview --}}
                    <div
                        id="backdropPreviewWrapper"
                        style="display:none; margin-top:15px;"
                    >

                        <p class="hint">
                            Xem trước Backdrop mới
                        </p>

                        <img
                            id="backdropPreview"
                            src=""
                            alt="Backdrop preview"
                            style="
                                display:block;
                                width:100%;
                                max-width:700px;
                                height:260px;
                                object-fit:cover;
                                border-radius:10px;
                                border:1px solid rgba(255,255,255,.12);
                            "
                        >

                    </div>

                </div>


                {{-- Status --}}
                <div class="field">

                    <label>
                        Trạng thái
                    </label>

                    <label
                        style="
                            display:flex;
                            align-items:center;
                            gap:8px;
                        "
                    >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $collection->is_active) ? 'checked' : '' }}
                        >

                        <span>
                            Đang hoạt động
                        </span>

                    </label>

                </div>


                {{-- Buttons --}}
                <div
                    style="
                        display:flex;
                        gap:10px;
                        margin-top:20px;
                    "
                >

                    <button
                        type="submit"
                        class="btn"
                    >
                        💾 Lưu thay đổi
                    </button>

                    <a
                        href="{{ route('admin.collections') }}"
                        class="btn ghost"
                    >
                        Hủy
                    </a>

                </div>

            </form>

        </div>

    </div>

</section>


<script>

    /**
     * Preview Poster mới
     */
    document.getElementById('poster').addEventListener('change', function (event) {

        const file = event.target.files[0];

        const preview = document.getElementById('posterPreview');
        const wrapper = document.getElementById('posterPreviewWrapper');

        if (!file) {

            wrapper.style.display = 'none';

            preview.src = '';

            return;
        }

        preview.src = URL.createObjectURL(file);

        wrapper.style.display = 'block';

    });


    /**
     * Preview Backdrop mới
     */
    document.getElementById('backdrop').addEventListener('change', function (event) {

        const file = event.target.files[0];

        const preview = document.getElementById('backdropPreview');
        const wrapper = document.getElementById('backdropPreviewWrapper');

        if (!file) {

            wrapper.style.display = 'none';

            preview.src = '';

            return;
        }

        preview.src = URL.createObjectURL(file);

        wrapper.style.display = 'block';

    });

</script>

@endsection