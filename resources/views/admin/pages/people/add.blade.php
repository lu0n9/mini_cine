@extends('admin.layouts.master')

@section('content')

<section id="add-people" class="page">

    <div class="page-head">

        <div>
            <h3>Thêm diễn viên / đạo diễn</h3>

            <p>
                Thêm hồ sơ diễn viên, đạo diễn hoặc producer vào hệ thống.
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
                action="{{ route('admin.people.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <div class="form-grid">


                    {{-- Tên --}}

                    <div class="field">

                        <label>Tên *</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
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
                            value="{{ old('slug') }}"
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
                            Hỗ trợ JPG, JPEG, PNG, WEBP. Tối đa 2MB.
                        </small>

                        {{-- Preview --}}
                        <div
                            id="avatar-preview-wrapper"
                            style="
                                display: none;
                                margin-top: 15px;
                            "
                        >

                            <img
                                id="avatar-preview"
                                src=""
                                alt="Preview ảnh"
                                style="
                                    width: 140px;
                                    height: 140px;
                                    object-fit: cover;
                                    border-radius: 8px;
                                "
                            >

                        </div>

                    </div>

                    {{-- Biography --}}

                    <div class="field full">

                        <label>Tiểu sử</label>

                        <textarea
                            name="biography"
                            rows="8"
                            placeholder="Nhập thông tin tiểu sử..."
                        >{{ old('biography') }}</textarea>

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
                            Lưu người
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
    const avatarPreviewWrapper = document.getElementById('avatar-preview-wrapper');


    avatarInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {

            avatarPreview.src = '';
            avatarPreviewWrapper.style.display = 'none';

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

            avatarPreview.src = '';
            avatarPreviewWrapper.style.display = 'none';

            return;
        }


        // Kiểm tra dung lượng
        if (file.size > 2 * 1024 * 1024) {

            alert('Ảnh không được vượt quá 2MB.');

            this.value = '';

            avatarPreview.src = '';
            avatarPreviewWrapper.style.display = 'none';

            return;
        }


        // Hiển thị preview
        const reader = new FileReader();

        reader.onload = function (event) {

            avatarPreview.src = event.target.result;

            avatarPreviewWrapper.style.display = 'block';

        };

        reader.readAsDataURL(file);

    });

});

</script>

@endsection