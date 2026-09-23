@extends('admin.layouts.master')

@section('content')
    <section id="users" class="page">

        {{-- HEADER --}}
        <div class="page-head">
            <div>
                <h3>Người dùng</h3>

                <p>
                    Quản lý tài khoản: khóa/mở, reset mật khẩu,
                    lịch sử hoạt động.
                </p>
            </div>

            <button class="btn" type="button">
                + Thêm user
            </button>
        </div>


        {{-- FILTER --}}
        <div class="table-tools">

            <a
                href="{{ route('admin.users') }}"
                class="filter {{ !request('status') ? 'on' : '' }}"
            >
                Tất cả
            </a>

            <a
                href="{{ route('admin.users', ['status' => 'active']) }}"
                class="filter {{ request('status') === 'active' ? 'on' : '' }}"
            >
                Active
            </a>

            <a
                href="{{ route('admin.users', ['status' => 'banned']) }}"
                class="filter {{ request('status') === 'banned' ? 'on' : '' }}"
            >
                Bị khóa
            </a>

            <a
                href="{{ route('admin.users', ['status' => 'unverified']) }}"
                class="filter {{ request('status') === 'unverified' ? 'on' : '' }}"
            >
                Chưa xác thực
            </a>

        </div>


        {{-- SUCCESS / ERROR --}}
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


        {{-- VALIDATION ERROR --}}
        @if($errors->any())
            <div class="alert alert-danger">

                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- USERS TABLE --}}
        <div class="panel">

            <table>

                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Đăng nhập cuối</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>


                <tbody>

                    @forelse($users as $user)

                        <tr>

                            {{-- USER --}}
                            <td>

                                <div class="movie-cell sm">

                                    @if($user->avatar)

                                        <img
                                            src="{{ asset(
                                                'storage/' . $user->avatar
                                            ) }}"
                                            alt="{{ $user->name }}"
                                        >

                                    @else

                                        <img
                                            src="/images/avatar-default.png"
                                            alt="{{ $user->name }}"
                                        >

                                    @endif


                                    <span class="mt">

                                        {{ $user->name }}

                                        <small>
                                            {{ '@' . \Illuminate\Support\Str::slug(
                                                $user->name
                                            ) }}
                                        </small>

                                    </span>

                                </div>

                            </td>


                            {{-- EMAIL --}}
                            <td>
                                {{ $user->email }}
                            </td>


                            {{-- ROLE --}}
                            <td>

                                <span class="tag">
                                    {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                                </span>

                            </td>


                            {{-- LAST LOGIN --}}
                            <td>

                                @if($user->last_login_at)

                                    {{ $user->last_login_at->diffForHumans() }}

                                @else

                                    Chưa đăng nhập

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($user->isBanned())

                                    <span class="status">
                                        Bị khóa
                                    </span>

                                    @if($user->ban_expires_at)

                                        <small style="display:block;">
                                            Hết hạn:
                                            {{ $user->ban_expires_at->format('d/m/Y H:i') }}
                                        </small>

                                    @else

                                        <small style="display:block;">
                                            Vĩnh viễn
                                        </small>

                                    @endif

                                @else

                                    <span class="status">
                                        Active
                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}
                            <td>

                                <div class="row-actions">

                                    {{-- EDIT --}}
                                    <!-- <button
                                        type="button"
                                        class="mini"
                                        title="Chỉnh sửa"
                                    >
                                        ✎
                                    </button> -->
                                    <a
                                        href="{{ route('admin.users.show', $user) }}"
                                        class="mini"
                                        title="Xem chi tiết"
                                    >
                                        👁
                                    </a>


                                    {{-- BAN --}}
                                    @if($user->role !== 'admin')

                                        @if($user->isBanned())

                                            {{-- UNBAN --}}
                                            <form
                                                action="{{ route(
                                                    'admin.users.unban',
                                                    $user
                                                ) }}"
                                                method="POST"
                                                style="display:inline;"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="mini"
                                                    title="Mở khóa"
                                                    onclick="return confirm(
                                                        'Bạn có chắc muốn mở khóa tài khoản này?'
                                                    )"
                                                >
                                                    🔓
                                                </button>

                                            </form>

                                        @else

                                            {{-- OPEN BAN MODAL --}}
                                            <button
                                                type="button"
                                                class="mini"
                                                title="Ban User"
                                                onclick="openBanModal(
                                                    {{ $user->id }},
                                                    @js($user->name),
                                                    @js($user->email)
                                                )"
                                            >
                                                🔒
                                            </button>

                                        @endif

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                style="text-align: center;"
                            >
                                Chưa có người dùng.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($users->hasPages())

            <div class="pagination">
                {{ $users->links() }}
            </div>

        @endif

    </section>


    {{-- ========================================= --}}
    {{-- BAN USER MODAL --}}
    {{-- ========================================= --}}

    <div
        id="banModal"
        class="ban-modal"
        style="display:none;">

        <div class="ban-modal-overlay" onclick="closeBanModal()"></div>


        <div class="ban-modal-content">

            <div class="ban-modal-header">

                <div>

                    <h3>Ban tài khoản</h3>

                    <p>
                        Khóa tài khoản người dùng
                    </p>

                </div>

                <button
                    type="button"
                    class="ban-modal-close"
                    onclick="closeBanModal()"
                >
                    ×
                </button>

            </div>


            {{-- USER INFO --}}
            <div class="ban-user-info">

                <strong id="banUserName"></strong>

                <small id="banUserEmail"></small>

            </div>


            {{-- FORM --}}
            <form
                id="banForm"
                method="POST"
                action=""
            >

                @csrf


                {{-- DURATION --}}
                <div class="field">

                    <label for="duration">
                        Thời gian ban
                    </label>

                    <select
                        name="duration"
                        id="duration"
                        required
                    >

                        <option value="">
                            -- Chọn thời gian --
                        </option>

                        <option value="1_day">
                            1 ngày
                        </option>

                        <option value="3_days">
                            3 ngày
                        </option>

                        <option value="7_days">
                            7 ngày
                        </option>

                        <option value="30_days">
                            30 ngày
                        </option>

                        <option value="permanent">
                            Vĩnh viễn
                        </option>

                    </select>

                </div>


                {{-- REASON --}}
                <div class="field">

                    <label for="reason">
                        Lý do ban
                    </label>

                    <select id="reason_type" onchange="setBanReason()">
                        <option value="">-- Chọn lý do --</option>
                        <option value="Vi phạm nội quy cộng đồng" selected>
                            Vi phạm nội quy cộng đồng
                        </option>
                        <option value="Spam hoặc quảng cáo">
                            Spam hoặc quảng cáo
                        </option>
                        <option value="Nội dung không phù hợp">
                            Nội dung không phù hợp
                        </option>
                        <option value="Có hành vi gây rối">
                            Có hành vi gây rối
                        </option>
                        <option value="Sử dụng tài khoản sai mục đích">
                            Sử dụng tài khoản sai mục đích
                        </option>
                        <option value="Khác">
                            Khác
                        </option>
                    </select>

                    <textarea
                        name="reason"
                        id="reason"
                        rows="5"
                        maxlength="5000"
                        required
                    >Vi phạm nội quy cộng đồng</textarea>

                </div>


                {{-- ACTIONS --}}
                <div class="ban-modal-actions">

                    <button
                        type="button"
                        class="btn"
                        id="banCancelBtn"
                        onclick="closeBanModal()"
                    >
                        Hủy
                    </button>

                    <button
                        type="submit"
                        class="btn"
                        id="banSubmitBtn"
                    >
                        🔒 Ban User
                    </button>

                </div>
                <div
                    id="banLoading"
                    style="display:none; text-align:center; margin-top:12px;"
                >
                    Đang khóa tài khoản và đưa email vào hàng đợi...
                </div>

            </form>

        </div>

    </div>


@endsection