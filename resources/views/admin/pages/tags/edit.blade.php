@extends('admin.layouts.master')

@section('content')

<section id="tags-edit" class="page">

    <div class="page-head">

        <div>
            <h3>Sửa Tag</h3>

            <p>
                Cập nhật thông tin Tag.
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


    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif


    <div class="panel">

        <div class="panel-body">

            <form
                action="{{ route('admin.tags.update', $tag) }}"
                method="POST"
            >

                @csrf
                @method('PUT')


                {{-- Tên Tag --}}
                <div class="field">

                    <label for="name">
                        Tên Tag <span style="color:red;">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $tag->name) }}"
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
                        value="{{ old('slug', $tag->slug) }}"
                        placeholder="Ví dụ: sieu-anh-hung"
                    >

                    <small>
                        Slug dùng cho URL và định danh Tag.
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
                    >{{ old('description', $tag->description) }}</textarea>

                </div>


                {{-- Thống kê --}}
                <div
                    style="
                        margin-top:15px;
                        padding:14px;
                        border-radius:8px;
                        background:rgba(255,255,255,.04);
                    "
                >

                    <strong>
                        Đang được sử dụng:
                    </strong>

                    {{ $tag->movies()->count() }} phim

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
                        Lưu thay đổi
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