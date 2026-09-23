@extends('admin.layouts.master')

@section('content')

<section id="tags-create" class="page">

    <div class="page-head">

        <div>
            <h3>Thêm Tag</h3>

            <p>
                Tạo một Tag mới để sử dụng cho phim.
            </p>
        </div>

        <a
            href="{{ route('admin.content.tags') }}"
            class="btn ghost"
        >
            ← Quay lại
        </a>

    </div>


    {{-- Validation errors --}}
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
                action="{{ route('admin.tags.store') }}"
                method="POST"
            >

                @csrf


                {{-- Tên Tag --}}
                <div class="field">

                    <label for="name">
                        Tên Tag <span style="color:red;">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ví dụ: Siêu anh hùng"
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
                        value="{{ old('slug') }}"
                        placeholder="Ví dụ: sieu-anh-hung"
                    >

                    <small>
                        Có thể để trống, hệ thống sẽ tự tạo Slug từ tên Tag.
                    </small>

                </div>


                {{-- Mô tả --}}
                <div class="field">

                    <label for="description">
                        Mô tả
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Mô tả ngắn về Tag..."
                    >{{ old('description') }}</textarea>

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
                        + Thêm Tag
                    </button>

                    <a
                        href="{{ route('admin.content.tags') }}"
                        class="btn ghost"
                    >
                        Hủy
                    </a>

                </div>

            </form>

        </div>

    </div>

</section>

@endsection