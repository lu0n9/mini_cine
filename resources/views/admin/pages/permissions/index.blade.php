@extends('admin.layouts.master')

@section('content')

<section id="permissions" class="page">

    {{-- HEADER --}}
    <div class="page-head">

        <div>
            <h3>Permissions</h3>

            <p>
                Quản lý các quyền được sử dụng trong hệ thống quản trị.
            </p>
        </div>


        @auth('admin')
            @if(auth('admin')->user()->hasPermission('roles.create'))

                <a
                    href="{{ route('admin.permissions.create') }}"
                    class="btn"
                >
                    + Thêm Permission
                </a>

            @endif
        @endauth

    </div>


    {{-- STATS --}}
    <div
        style="
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:16px;
            margin-bottom:20px;
        "
    >

        <div class="panel" style="padding:20px;">

            <small style="display:block; opacity:.7;">
                Tổng Permission
            </small>

            <strong
                style="
                    display:block;
                    font-size:28px;
                    margin-top:5px;
                "
            >
                {{ $totalPermissions }}
            </strong>

        </div>


        <div class="panel" style="padding:20px;">

            <small style="display:block; opacity:.7;">
                Tổng Module
            </small>

            <strong
                style="
                    display:block;
                    font-size:28px;
                    margin-top:5px;
                "
            >
                {{ $totalModules }}
            </strong>

        </div>

    </div>


    {{-- FILTER --}}
    <div class="table-tools">

        <form
            method="GET"
            action="{{ route('admin.permissions') }}"
            style="
                display:flex;
                gap:10px;
                width:100%;
                flex-wrap:wrap;
            "
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm Permission..."
                style="
                    flex:1;
                    min-width:220px;
                    padding:10px 14px;
                    border:1px solid #ddd;
                    border-radius:8px;
                "
            >


            <select
                name="module"
                style="
                    min-width:180px;
                    padding:10px 14px;
                    border:1px solid #ddd;
                    border-radius:8px;
                "
            >

                <option value="">
                    Tất cả Module
                </option>

                @foreach($modules as $module)

                    <option
                        value="{{ $module }}"
                        {{ request('module') === $module ? 'selected' : '' }}
                    >
                        {{ $module }}
                    </option>

                @endforeach

            </select>


            <button
                type="submit"
                class="btn"
            >
                🔍 Tìm kiếm
            </button>


            @if(request('search') || request('module'))

                <a
                    href="{{ route('admin.permissions') }}"
                    class="btn"
                >
                    Xóa lọc
                </a>

            @endif

        </form>

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


    {{-- TABLE --}}
    <div class="panel">

        <table>

            <thead>

                <tr>

                    <th>Permission</th>
                    <th>Slug</th>
                    <th>Module</th>
                    <th>Mô tả</th>
                    <th>Số Role</th>
                    <th></th>

                </tr>

            </thead>


            <tbody>

                @forelse($permissions as $permission)

                    <tr>

                        {{-- NAME --}}
                        <td>

                            <strong>
                                {{ $permission->name }}
                            </strong>

                        </td>


                        {{-- SLUG --}}
                        <td>

                            <code>
                                {{ $permission->slug }}
                            </code>

                        </td>


                        {{-- MODULE --}}
                        <td>

                            <span class="tag">
                                {{ $permission->module }}
                            </span>

                        </td>


                        {{-- DESCRIPTION --}}
                        <td>

                            @if($permission->description)

                                {{ $permission->description }}

                            @else

                                <span style="opacity:.5;">
                                    Chưa có mô tả
                                </span>

                            @endif

                        </td>


                        {{-- ROLE COUNT --}}
                        <td>

                            @if($permission->roles_count > 0)

                                <span class="tag solid">
                                    {{ $permission->roles_count }} 
                                </span>

                            @else

                                <span class="tag">
                                    Chưa sử dụng
                                </span>

                            @endif

                        </td>


                        {{-- ACTIONS --}}
                        <td>

                            <div class="row-actions">

                                {{-- VIEW --}}
                                @auth('admin')
                                    @if(auth('admin')->user()->hasPermission('roles.view'))

                                        <a
                                            href="{{ route('admin.permissions.show', $permission) }}"
                                            class="mini"
                                            title="Xem"
                                        >
                                            👁
                                        </a>

                                    @endif
                                @endauth


                                {{-- EDIT --}}
                                @auth('admin')
                                    @if(auth('admin')->user()->hasPermission('roles.edit'))

                                        <a
                                            href="{{ route('admin.permissions.edit', $permission) }}"
                                            class="mini"
                                            title="Chỉnh sửa"
                                        >
                                            ✎
                                        </a>

                                    @endif
                                @endauth


                                {{-- DELETE --}}
                                @auth('admin')
                                    @if(auth('admin')->user()->hasPermission('roles.delete'))

                                        <form
                                            action="{{ route('admin.permissions.destroy', $permission) }}"
                                            method="POST"
                                            style="display:inline;"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="mini"
                                                title="Xóa"
                                                onclick="return confirm(
                                                    'Bạn có chắc muốn xóa Permission này?'
                                                )"
                                            >
                                                ✕
                                            </button>

                                        </form>

                                    @endif
                                @endauth

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;"
                        >
                            Không có Permission.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($permissions->hasPages())

        <div class="pagination">
            {{ $permissions->links('pagination::custom') }}
        </div>

    @endif

</section>

@endsection