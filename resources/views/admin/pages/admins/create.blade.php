@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>
            <h3>Thêm Admin</h3>

            <p>
                Tạo tài khoản quản trị viên mới và phân quyền.
            </p>
        </div>

        <a
            href="{{ route('admin.admins') }}"
            class="btn"
        >
            ← Quay lại
        </a>

    </div>


    {{-- ERROR --}}
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
            action="{{ route('admin.admins.store') }}"
        >

            @csrf


            {{-- NAME --}}
            <div class="field">

                <label for="name">
                    Họ tên
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nhập tên Admin"
                    required
                >

            </div>


            {{-- EMAIL --}}
            <div class="field">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="admin@example.com"
                    required
                >

            </div>


            {{-- PASSWORD --}}
            <div class="field">

                <label for="password">
                    Mật khẩu
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ít nhất 8 ký tự"
                    required
                >

            </div>


            {{-- PASSWORD CONFIRM --}}
            <div class="field">

                <label for="password_confirmation">
                    Xác nhận mật khẩu
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Nhập lại mật khẩu"
                    required
                >

            </div>


            {{-- ROLE --}}
            <div class="field">

                <label for="role_id">
                    Role
                </label>

                <select
                    name="role_id"
                    id="role_id"
                    required
                >

                    <option value="">
                        -- Chọn Role --
                    </option>

                    @foreach($roles as $role)

                        @php
                            $isSuperAdmin = $role->is_super_admin;

                            $canAssignSuperAdmin =
                                auth('admin')->user()
                                    ->roles()
                                    ->where('is_super_admin', true)
                                    ->where('is_active', true)
                                    ->exists();
                        @endphp

                        @if(!$isSuperAdmin || $canAssignSuperAdmin)

                            <option
                                value="{{ $role->id }}"
                                {{ old('role_id') == $role->id ? 'selected' : '' }}
                            >
                                {{ $role->name }}

                                @if($role->is_super_admin)
                                    (Toàn quyền)
                                @endif

                            </option>

                        @endif

                    @endforeach

                </select>

                <small>
                    Role quyết định các quyền mà tài khoản Admin được sử dụng.
                </small>

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
                    href="{{ route('admin.admins') }}"
                    class="btn"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn"
                >
                    + Tạo Admin
                </button>

            </div>

        </form>

    </div>

</section>

@endsection