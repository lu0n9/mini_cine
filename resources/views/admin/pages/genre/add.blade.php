@extends('admin.layouts.master')

@section('content')

<section id="add-genre" class="page">

    <div class="page-head">

        <div>

            <h3>Thêm thể loại</h3>

            <p>
                Thêm thể loại phim vào hệ thống.
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
                action="{{ route('admin.genres.store') }}"
                method="POST"
            >

                @csrf

                <div class="form-grid">


                    {{-- Tên thể loại --}}

                    <div class="field">

                        <label>Tên thể loại *</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
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
                            value="{{ old('slug') }}"
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
                            Lưu thể loại
                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>

</section>

@endsection