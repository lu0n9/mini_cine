@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>
            <h3>Chỉnh sửa Admin</h3>

            <p>
                Cập nhật thông tin và Role của tài khoản quản trị viên.
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
            action="{{ route('admin.admins.update', $admin) }}"
        >

            @csrf

            @method('PUT')


            {{-- NAME --}}
            <div class="field">

                <label for="name">
                    Họ tên
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $admin->name) }}"
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
                    value="{{ old('email', $admin->email) }}"
                    required
                >

            </div>


            {{-- PASSWORD --}}
            <div class="field">

                <label for="password">
                    Mật khẩu mới
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Để trống nếu không muốn đổi mật khẩu"
                >

            </div>


            {{-- PASSWORD CONFIRM --}}
            <div class="field">

                <label for="password_confirmation">
                    Xác nhận mật khẩu mới
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Nhập lại mật khẩu mới"
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
                                {{
                                    old(
                                        'role_id',
                                        $currentRoleId
                                    ) == $role->id
                                        ? 'selected'
                                        : ''
                                }}
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
                    Thay đổi Role sẽ thay đổi quyền của tài khoản này.
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
                    💾 Lưu thay đổi
                </button>

            </div>

        </form>

    </div>

</section>

@endsection