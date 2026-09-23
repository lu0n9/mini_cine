@extends('admin.layouts.master')

@section('content')

<section id="edit-genre" class="page">

    <div class="page-head">

        <div>

            <h3>Chỉnh sửa thể loại</h3>

            <p>
                Cập nhật thông tin thể loại phim.
            </p>

        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.content.genres') }}"
        >
            ← Quay lại
        </a>

    </div>


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.genres.update', $genre->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="form-grid">


                    {{-- Tên thể loại --}}

                    <div class="field">

                        <label>Tên thể loại *</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $genre->name) }}"
                            placeholder="Ví dụ: Hành động"
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
                            value="{{ old('slug', $genre->slug) }}"
                            placeholder="Ví dụ: hanh-dong"
                            maxlength="255"
                        >

                        @error('slug')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                        <small>
                            Có thể để trống, hệ thống sẽ tự tạo slug.
                        </small>

                    </div>


                    {{-- Actions --}}

                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.content.genres') }}"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Cập nhật thể loại
                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>

</section>

@endsection