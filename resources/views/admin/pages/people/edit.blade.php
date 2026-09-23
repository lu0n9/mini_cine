@extends('admin.layouts.master')

@section('content')

<section id="edit-people" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa diễn viên / đạo diễn</h3>

            <p>
                Chỉnh sửa thông tin hồ sơ của diễn viên, đạo diễn hoặc producer.
            </p>
        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.people') }}"
        >
            ← Quay lại
        </a>

    </div>


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.people.update', $person->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')

                <div class="form-grid">


                    {{-- Tên --}}

                    <div class="field">

                        <label>Tên *</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $person->name) }}"
                            placeholder="Ví dụ: Leonardo DiCaprio"
                            maxlength="255"
                            required
                        >

                        @error('name')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Slug --}}

                    <div class="field">

                        <label>Slug</label>

                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug', $person->slug) }}"
                            placeholder="Ví dụ: leonardo-dicaprio"
                            maxlength="255"
                        >

                        @error('slug')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                        <small>
                            Có thể để trống, hệ thống sẽ tự tạo slug từ tên.
                        </small>

                    </div>


                    {{-- Avatar --}}

                    <div class="field full">

                        <label>Ảnh đại diện</label>

                        <input
                            type="file"
                            name="avatar"
                            id="avatar"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        @error('avatar')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                        <small>
                            Chọn ảnh mới nếu muốn thay ảnh hiện tại.
                            Bỏ trống nếu muốn giữ ảnh cũ.
                        </small>


                        {{-- Preview ảnh --}}

                        <div
                            id="avatar-preview-wrapper"
                            style="
                                margin-top: 15px;
                            "
                        >

                            @if($person->avatar)

                                <img
                                    id="avatar-preview"
                                    src="{{ asset('storage/' . $person->avatar) }}"
                                    alt="{{ $person->name }}"
                                    style="
                                        width: 140px;
                                        height: 140px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                    "
                                >

                            @else

                                <img
                                    id="avatar-preview"
                                    src=""
                                    alt="Preview ảnh"
                                    style="
                                        display: none;
                                        width: 140px;
                                        height: 140px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                    "
                                >

                            @endif

                        </div>

                    </div>


                    {{-- Biography --}}

                    <div class="field full">

                        <label>Tiểu sử</label>

                        <textarea
                            name="biography"
                            rows="8"
                            placeholder="Nhập thông tin tiểu sử..."
                        >{{ old('biography', $person->biography) }}</textarea>

                        @error('biography')
                            <small class="field-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Actions --}}

                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.people') }}"
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

<script>

document.addEventListener('DOMContentLoaded', function () {

    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');


    avatarInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        // Kiểm tra loại file
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert('Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');

            this.value = '';

            return;
        }


        // Kiểm tra dung lượng
        if (file.size > 2 * 1024 * 1024) {

            alert('Ảnh không được vượt quá 2MB.');

            this.value = '';

            return;
        }


        // Hiển thị ảnh mới
        const reader = new FileReader();

        reader.onload = function (event) {

            avatarPreview.src = event.target.result;

            avatarPreview.style.display = 'block';

        };

        reader.readAsDataURL(file);

    });

});

</script>

@endsection