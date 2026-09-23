@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>

            <h3>Chỉnh sửa Permission</h3>

            <p>
                Cập nhật thông tin Permission.
            </p>

        </div>

        <a
            href="{{ route('admin.permissions') }}"
            class="btn"
        >
            ← Quay lại
        </a>

    </div>


    {{-- ERRORS --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul style="margin:0; padding-left:20px;">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="panel">

        <form
            method="POST"
            action="{{ route('admin.permissions.update', $permission) }}"
        >

            @csrf

            @method('PUT')


            {{-- NAME --}}
            <div class="field">

                <label for="name">
                    Tên Permission
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $permission->name) }}"
                    required
                >

            </div>


            {{-- SLUG --}}
            <div class="field">

                <label for="slug">
                    Slug
                </label>

                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $permission->slug) }}"
                    required
                >

                <small>
                    Ví dụ: movies.view, users.edit, reports.handle
                </small>

            </div>


            {{-- MODULE --}}
            <div class="field">

                <label for="module">
                    Module
                </label>

                <input
                    type="text"
                    id="module"
                    name="module"
                    value="{{ old('module', $permission->module) }}"
                    required
                >

            </div>


            {{-- DESCRIPTION --}}
            <div class="field">

                <label for="description">
                    Mô tả
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    maxlength="1000"
                >{{ old('description', $permission->description) }}</textarea>

            </div>


            {{-- INFO --}}
            @if($permission->roles()->count() > 0)

                <div
                    style="
                        padding:14px;
                        margin-top:15px;
                        border-radius:8px;
                        background:#fff8e1;
                    "
                >
                    ⚠️ Permission này hiện đang được
                    <strong>
                        {{ $permission->roles()->count() }}
                    </strong>
                    Role sử dụng.
                </div>

            @endif


            {{-- ACTION --}}
            <div
                style="
                    display:flex;
                    gap:10px;
                    margin-top:20px;
                "
            >

                <a
                    href="{{ route('admin.permissions') }}"
                    class="btn"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn"
                >
                    💾 Lưu thay đổi
                </button>

            </div>

        </form>

    </div>

</section>

@endsection