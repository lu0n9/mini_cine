@extends('admin.layouts.master')

@section('content')

<section id="admins" class="page">

    {{-- HEADER --}}
    <div class="page-head">
        <div>
            <h3>Quản trị viên</h3>

            <p>
                Quản lý tài khoản quản trị viên và phân quyền hệ thống.
            </p>
        </div>

        @auth('admin')
            @if(auth('admin')->user()->hasPermission('staff.create'))
                <a
                    href="{{ route('admin.admins.create') }}"
                    class="btn"
                >
                    + Thêm Admin
                </a>
            @endif
        @endauth
    </div>


    {{-- SEARCH --}}
    <div class="table-tools">

        <form
            method="GET"
            action="{{ route('admin.admins') }}"
            style="display:flex; gap:10px; width:100%;"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm theo tên hoặc email..."
                style="
                    flex:1;
                    min-width:200px;
                    padding:10px 14px;
                    border:1px solid #ddd;
                    border-radius:8px;
                "
            >

            <button
                type="submit"
                class="btn"
            >
                🔍 Tìm kiếm
            </button>

            @if(request('search'))
                <a
                    href="{{ route('admin.admins') }}"
                    class="btn"
                >
                    Xóa
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


    {{-- VALIDATION ERROR --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul style="margin:0; padding-left:20px;">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ADMINS TABLE --}}
    <div class="panel">

        <table>

            <thead>

                <tr>

                    <th>Quản trị viên</th>

                    <th>Email</th>

                    <th>Role</th>

                    <th>Ngày tạo</th>

                    <th></th>

                </tr>

            </thead>


            <tbody>

                @forelse($admins as $admin)

                    <tr>

                        {{-- ADMIN --}}
                        <td>

                            <div class="movie-cell sm">

                                <div
                                    style="
                                        width:40px;
                                        height:40px;
                                        border-radius:50%;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        background:#eee;
                                        font-weight:600;
                                    "
                                >
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>

                                <span class="mt">

                                    {{ $admin->name }}

                                    <small>
                                        {{ '@' . \Illuminate\Support\Str::slug($admin->name) }}
                                    </small>

                                </span>

                            </div>

                        </td>


                        {{-- EMAIL --}}
                        <td>

                            {{ $admin->email }}

                        </td>


                        {{-- ROLE --}}
                        <td>

                            @if($admin->roles->isEmpty())

                                <span class="tag">
                                    Chưa phân quyền
                                </span>

                            @else

                                @foreach($admin->roles as $role)

                                    <span
                                        class="tag {{ $role->is_super_admin ? 'solid' : '' }}"
                                        style="margin-right:4px;"
                                    >
                                        {{ $role->name }}
                                    </span>

                                @endforeach

                            @endif

                        </td>


                        {{-- CREATED --}}
                        <td>

                            {{ $admin->created_at
                                ? $admin->created_at->format('d/m/Y H:i')
                                : '-' }}

                        </td>


                        {{-- ACTIONS --}}
                        <td>

                            <div class="row-actions">

                                {{-- EDIT --}}
                                @auth('admin')
                                    @if(auth('admin')->user()->hasPermission('staff.create'))
                                    <a
                                        href="{{route('admin.admins.edit',$admin)}}"
                                        class="mini"
                                        title="Chỉnh sửa"
                                    >
                                        ✎
                                    </a>
                                    @endif
                                @endauth


                                {{-- VIEW --}}
                                <a
                                    href="#"
                                    class="mini"
                                    title="Xem chi tiết"
                                >
                                    👁
                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            style="text-align:center;"
                        >
                            @if(request('search'))

                                Không tìm thấy tài khoản Admin phù hợp.

                            @else

                                Chưa có tài khoản Admin.

                            @endif
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($admins->hasPages())

        <div class="pagination">

            {{ $admins->links('pagination::custom') }}

        </div>

    @endif

</section>

@endsection