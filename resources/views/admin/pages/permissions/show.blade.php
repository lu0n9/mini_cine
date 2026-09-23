@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>

            <h3>Chi tiết Permission</h3>

            <p>
                Thông tin và các Role đang sử dụng quyền này.
            </p>

        </div>

        <div
            style="
                display:flex;
                gap:10px;
            "
        >

            <a
                href="{{ route('admin.permissions') }}"
                class="btn"
            >
                ← Quay lại
            </a>

            @auth('admin')

                @if(auth('admin')->user()->hasPermission('roles.edit'))

                    <a
                        href="{{ route('admin.permissions.edit', $permission) }}"
                        class="btn"
                    >
                        ✎ Chỉnh sửa
                    </a>

                @endif

            @endauth

        </div>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- INFORMATION --}}
    <div class="panel">

        <div
            style="
                padding:20px;
            "
        >

            <div
                style="
                    display:grid;
                    grid-template-columns:repeat(2, minmax(0, 1fr));
                    gap:20px;
                "
            >

                {{-- NAME --}}
                <div>

                    <small style="opacity:.6;">
                        Tên Permission
                    </small>

                    <h3 style="margin-top:5px;">
                        {{ $permission->name }}
                    </h3>

                </div>


                {{-- SLUG --}}
                <div>

                    <small style="opacity:.6;">
                        Slug
                    </small>

                    <div style="margin-top:5px;">

                        <code>
                            {{ $permission->slug }}
                        </code>

                    </div>

                </div>


                {{-- MODULE --}}
                <div>

                    <small style="opacity:.6;">
                        Module
                    </small>

                    <div style="margin-top:5px;">

                        <span class="tag">
                            {{ $permission->module }}
                        </span>

                    </div>

                </div>


                {{-- CREATED --}}
                <div>

                    <small style="opacity:.6;">
                        Ngày tạo
                    </small>

                    <div style="margin-top:5px;">

                        {{ $permission->created_at
                            ? $permission->created_at->format('d/m/Y H:i')
                            : '-' }}

                    </div>

                </div>

            </div>


            {{-- DESCRIPTION --}}
            <div style="margin-top:25px;">

                <small style="opacity:.6;">
                    Mô tả
                </small>

                <p style="margin-top:5px;">

                    {{ $permission->description ?: 'Chưa có mô tả.' }}

                </p>

            </div>

        </div>

    </div>


    {{-- ROLES --}}
    <div class="panel">

        <div
            style="
                padding:20px;
                border-bottom:1px solid #eee;
            "
        >

            <h3 style="margin:0;">
                Role sử dụng Permission
            </h3>

            <p style="margin:5px 0 0; opacity:.7;">
                Các Role hiện đang được cấp quyền này.
            </p>

        </div>


        <table>

            <thead>

                <tr>

                    <th>Role</th>

                    <th>Slug</th>

                    <th>Số Admin</th>

                    <th>Trạng thái</th>

                </tr>

            </thead>


            <tbody>

                @forelse($permission->roles as $role)

                    <tr>

                        {{-- ROLE --}}
                        <td>

                            <span
                                class="tag {{ $role->is_super_admin ? 'solid' : '' }}"
                            >
                                {{ $role->name }}
                            </span>

                        </td>


                        {{-- SLUG --}}
                        <td>

                            <code>
                                {{ $role->slug }}
                            </code>

                        </td>


                        {{-- ADMINS --}}
                        <td>

                            {{ $role->admins_count }}

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($role->is_active)

                                <span class="status">
                                    Active
                                </span>

                            @else

                                <span class="status">
                                    Inactive
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            style="text-align:center;"
                        >
                            Permission này chưa được gán
                            cho Role nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>

@endsection