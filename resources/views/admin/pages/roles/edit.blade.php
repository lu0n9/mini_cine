@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>
            <h3>Sửa Role</h3>
            <p>Chỉnh sửa thông tin và quyền của Role.</p>
        </div>

        <a
            href="{{ route('admin.roles.index') }}"
            class="btn"
        >
            ← Quay lại
        </a>

    </div>


    @if ($errors->any())

        <div class="alert alert-error">

            <strong>Có lỗi xảy ra:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        action="{{ route('admin.roles.update', $role) }}"
        method="POST"
    >

        @csrf
        @method('PUT')


        {{-- THÔNG TIN ROLE --}}

        <div class="panel">

            <div class="panel-head">
                <h4>Thông tin Role</h4>
            </div>


            <div class="form-grid">

                <div class="field">

                    <label for="name">
                        Tên Role
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $role->name) }}"
                        required
                    >

                    @error('name')
                        <small class="error-text">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                <div class="field">

                    <label for="slug">
                        Slug
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $role->slug) }}"
                        {{ $role->is_super_admin ? 'readonly' : '' }}
                    >

                    @error('slug')
                        <small class="error-text">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                <div class="field full">

                    <label for="description">
                        Mô tả
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                    >{{ old('description', $role->description) }}</textarea>

                </div>

            </div>

        </div>


        {{-- SUPER ADMIN --}}

        @if($role->is_super_admin)

            <div class="panel">

                <div class="alert alert-info">

                    <strong>Super Admin</strong>

                    <p>
                        Role này có toàn quyền trong hệ thống.
                        Không cần chọn permission riêng.
                    </p>

                </div>

            </div>

        @else


            {{-- PERMISSIONS --}}

            <div class="panel">

                <div class="panel-head">

                    <div>
                        <h4>Phân quyền</h4>

                        <p>
                            Chọn những quyền mà Role này được phép sử dụng.
                        </p>
                    </div>


                    <label class="select-all">

                        <input
                            type="checkbox"
                            id="checkAll"
                        >

                        <span>
                            Chọn tất cả
                        </span>

                    </label>

                </div>


                @forelse($permissions as $module => $modulePermissions)

                    <div class="permission-group">

                        <div class="permission-group-head">

                            <strong>
                                {{ ucfirst(str_replace('_', ' ', $module)) }}
                            </strong>


                            <label>

                                <input
                                    type="checkbox"
                                    class="module-check-all"
                                    data-module="{{ $module }}"
                                >

                                Chọn module

                            </label>

                        </div>


                        <div class="permission-list">

                            @foreach($modulePermissions as $permission)

                                @php

                                    $checked =
                                        in_array(
                                            $permission->id,
                                            old(
                                                'permissions',
                                                $rolePermissionIds
                                            )
                                        );

                                @endphp


                                <label class="permission-item">

                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        class="permission-checkbox module-{{ $module }}"
                                        data-module="{{ $module }}"
                                        {{ $checked ? 'checked' : '' }}
                                    >


                                    <span>

                                        <strong>
                                            {{ $permission->name }}
                                        </strong>

                                        <small>
                                            {{ $permission->slug }}
                                        </small>


                                        @if($permission->description)

                                            <em>
                                                {{ $permission->description }}
                                            </em>

                                        @endif

                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>

                @empty

                    <div class="empty-state">
                        Chưa có permission nào trong hệ thống.
                    </div>

                @endforelse

            </div>

        @endif


        {{-- BUTTON --}}

        <div class="form-actions">

            <a
                href="{{ route('admin.roles.index') }}"
                class="btn"
            >
                Hủy
            </a>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Lưu thay đổi
            </button>

        </div>

    </form>

</section>

@endsection