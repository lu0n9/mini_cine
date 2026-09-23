@extends('admin.layouts.master')

@section('content')

<section id="add-country" class="page">

    <div class="page-head">

        <div>

            <h3>Thêm quốc gia</h3>

            <p>
                Thêm quốc gia sản xuất phim vào hệ thống.
            </p>

        </div>

        <a
            class="btn ghost"
            href="{{ route('admin.countries') }}"
        >
            ← Quay lại
        </a>

    </div>


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.countries.store') }}"
                method="POST"
            >

                @csrf

                <div class="form-grid">


                    {{-- Tên quốc gia --}}

                    <div class="field">

                        <label>Tên quốc gia *</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Hàn Quốc"
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
                            placeholder="Ví dụ: han-quoc"
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


                    {{-- Code --}}

                    <div class="field">

                        <label>Mã quốc gia *</label>

                        <input
                            type="text"
                            name="code"
                            value="{{ old('code') }}"
                            placeholder="Ví dụ: KR"
                            maxlength="10"
                            style="text-transform: uppercase;"
                            required
                        >

                        @error('code')

                            <small class="field-error">
                                {{ $message }}
                            </small>

                        @enderror

                        <small>
                            Ví dụ: US, KR, JP, VN.
                        </small>

                    </div>


                    {{-- Actions --}}

                    <div class="form-actions">

                        <a
                            class="btn ghost"
                            href="{{ route('admin.countries') }}"
                        >
                            Hủy
                        </a>

                        <button
                            type="submit"
                            class="btn"
                        >
                            Lưu quốc gia
                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>

</section>

@endsection