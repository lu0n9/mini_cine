@extends('admin.layouts.master')

@section('content')

<section id="roles" class="page">

    <div class="page-head">

        <div>
            <h3>Roles</h3>
            <p>Nhóm quyền cho quản trị viên và nhân viên.</p>
        </div>

        <a href="{{ route('admin.roles.create') }}" class="btn">
            + Thêm role
        </a>

    </div>


    <div class="panel">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        <table>

            <thead>
                <tr>
                    <th>Role</th>
                    <th>Số quyền</th>
                    <th>Số người</th>
                    <th></th>
                </tr>
            </thead>


            <tbody>

                @forelse($roles as $role)

                    <tr>

                        <td>

                            <span class="tag {{ $role->is_super_admin ? 'solid' : '' }}">
                                {{ $role->name }}
                            </span>

                        </td>


                        <td>

                            @if($role->is_super_admin)

                                Toàn quyền

                            @else

                                {{ $role->permissions_count }}

                            @endif

                        </td>


                        <td>
                            {{ $role->admins_count }}
                        </td>


                        <td>

                            <div class="row-actions">

                                <a
                                    href="{{ route('admin.roles.edit', $role) }}"
                                    class="mini"
                                    title="Chỉnh sửa"
                                >
                                    ✎
                                </a>


                                @if(!$role->is_super_admin)

                                    <form
                                        action="{{ route('admin.roles.destroy', $role) }}"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa Role này?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="mini"
                                            title="Xóa"
                                        >
                                            ✕
                                        </button>

                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" style="text-align:center;">
                            Chưa có Role nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($roles->hasPages())
        <div class="pagination">
            {{ $roles->links('pagination::custom') }}
        </div>
    @endif

</section>

@endsection