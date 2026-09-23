@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>

            <h3>Thêm Permission</h3>

            <p>
                Tạo một quyền mới cho hệ thống RBAC.
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
            action="{{ route('admin.permissions.store') }}"
        >

            @csrf


            {{-- NAME --}}
            <div class="field">

                <label for="name">
                    Tên Permission
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Ví dụ: Xem phim"
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
                    value="{{ old('slug') }}"
                    placeholder="Ví dụ: movies.view"
                    required
                >

                <small>
                    Slug được sử dụng trong middleware RBAC.
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
                    value="{{ old('module') }}"
                    placeholder="Ví dụ: movies"
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
                    placeholder="Mô tả quyền này..."
                >{{ old('description') }}</textarea>

            </div>


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
                    + Tạo Permission
                </button>

            </div>

        </form>

    </div>

</section>

@endsection