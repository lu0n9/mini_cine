@extends('admin.layouts.master')

@section('content')

<section id="user-detail" class="page">

    {{-- PAGE HEADER --}}
    <div class="page-head">
        <div>
            <h3>Chi tiết người dùng</h3>
            <p>Thông tin và hoạt động của tài khoản</p>
        </div>

        <a href="{{ route('admin.users') }}" class="btn">
            ← Quay lại
        </a>
    </div>


    {{-- USER HEADER --}}
    <div class="panel">
        <div class="user-detail-header">

            <div class="movie-cell sm">

                @if($user->avatar)
                    <img
                        src="{{ asset('storage/' . $user->avatar) }}"
                        alt="{{ $user->name }}"
                    >
                @else
                    <img
                        src="/images/avatar-default.png"
                        alt="{{ $user->name }}"
                    >
                @endif

                <span class="mt">
                    <strong>{{ $user->name }}</strong>
                    <small>{{ $user->email }}</small>
                </span>

            </div>

            <div
                class="user-detail-actions"
                style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                "
            >

                @if($user->role !== 'admin')

                    @if($user->isBanned())

                        {{-- STATUS --}}
                        <span
                            class="status"
                            style="
                                display:inline-flex;
                                align-items:center;
                                height:30px;
                                padding:0 10px;
                                border:1px solid #e5e5e5;
                                border-radius:6px;
                                background:#f7f7f7;
                                color:#555;
                                font-size:12px;
                                font-weight:600;
                                line-height:1;
                            "
                        >
                            Bị khóa
                        </span>

                        {{-- UNBAN --}}
                        <form
                            action="{{ route('admin.users.unban', $user) }}"
                            method="POST"
                            style="display:inline-flex;margin:0;"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                title="Mở khóa"
                                style="
                                    height:30px;
                                    padding:0 11px;
                                    border:1px solid #e5e5e5;
                                    border-radius:6px;
                                    background:#fff;
                                    color:#222;
                                    display:inline-flex;
                                    align-items:center;
                                    justify-content:center;
                                    font-size:12px;
                                    font-weight:600;
                                    line-height:1;
                                    cursor:pointer;
                                "
                                onclick="return confirm(
                                    'Bạn có chắc muốn mở khóa tài khoản này?'
                                )"
                            >
                                Unban
                            </button>
                        </form>

                    @else

                        {{-- STATUS --}}
                        <span
                            class="status"
                            style="
                                display:inline-flex;
                                align-items:center;
                                height:30px;
                                padding:0 10px;
                                border:1px solid #e5e5e5;
                                border-radius:6px;
                                background:#fff;
                                color:#555;
                                font-size:12px;
                                font-weight:600;
                                line-height:1;
                            "
                        >
                            Active
                        </span>

                        {{-- BAN --}}
                        <button
                            type="button"
                            title="Ban User"
                            style="
                                height:30px;
                                padding:0 12px;
                                border:1px solid #111;
                                border-radius:6px;
                                background:#111;
                                color:#fff;
                                display:inline-flex;
                                align-items:center;
                                justify-content:center;
                                font-size:12px;
                                font-weight:600;
                                line-height:1;
                                cursor:pointer;
                            "
                            onclick="openBanModal(
                                {{ $user->id }},
                                @js($user->name),
                                @js($user->email)
                            )"
                        >
                            Ban
                        </button>

                    @endif

                @else

                    {{-- ADMIN --}}
                    <span
                        class="status"
                        style="
                            display:inline-flex;
                            align-items:center;
                            height:30px;
                            padding:0 10px;
                            border:1px solid #e5e5e5;
                            border-radius:6px;
                            background:#f7f7f7;
                            color:#222;
                            font-size:12px;
                            font-weight:600;
                            line-height:1;
                        "
                    >
                        Admin
                    </span>

                @endif

            </div>

        </div>
    </div>


    {{-- TABS --}}
    <div class="panel user-detail-tabs">

        <a
            href="{{ route('admin.users.show', $user) }}?tab=overview"
            class="{{ $tab === 'overview' ? 'active' : '' }}"
        >
            Tổng quan
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=activity"
            class="{{ $tab === 'activity' ? 'active' : '' }}"
        >
            Hoạt động
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=favorites"
            class="{{ $tab === 'favorites' ? 'active' : '' }}"
        >
            Phim yêu thích
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=comments"
            class="{{ $tab === 'comments' ? 'active' : '' }}"
        >
            Comment
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=ratings"
            class="{{ $tab === 'ratings' ? 'active' : '' }}"
        >
            Rating
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=devices"
            class="{{ $tab === 'devices' ? 'active' : '' }}"
        >
            Thiết bị / IP
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=account"
            class="{{ $tab === 'account' ? 'active' : '' }}"
        >
            Tài khoản
        </a>

        <a
            href="{{ route('admin.users.show', $user) }}?tab=notifications"
            class="{{ $tab === 'notifications' ? 'active' : '' }}"
        >
            Thông báo
        </a>

    </div>


    {{-- TAB CONTENT --}}
    @if($tab === 'overview')

        {{-- STATISTICS --}}
        <div class="user-stats">

            <div class="panel user-stat">
                <span class="user-stat-label">Tham gia</span>

                <strong>
                    {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                </strong>

                <small>
                    {{ $user->created_at?->diffForHumans() ?? '—' }}
                </small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Tổng thời gian xem</span>

                @php
                    $hours = intdiv((int) $totalWatchTime, 3600);
                    $minutes = intdiv(((int) $totalWatchTime % 3600), 60);
                @endphp

                <strong>
                    @if($hours > 0)
                        {{ $hours }} giờ {{ $minutes }} phút
                    @else
                        {{ $minutes }} phút
                    @endif
                </strong>

                <small>Thời gian đã xem phim</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Phim đã xem</span>

                <strong>
                    {{ number_format($totalMoviesWatched, 0, ',', '.') }}
                </strong>

                <small>Phim khác nhau</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Tổng lượt xem</span>

                <strong>
                    {{ number_format($totalViews, 0, ',', '.') }}
                </strong>

                <small>Lượt xem trên hệ thống</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Comment</span>

                <strong>
                    {{ number_format($totalComments, 0, ',', '.') }}
                </strong>

                <small>Bình luận đã đăng</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Rating</span>

                <strong>
                    {{ number_format($totalRatings, 0, ',', '.') }}
                </strong>

                <small>Lượt đánh giá</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Phim yêu thích</span>

                <strong>
                    {{ number_format($totalFavorites, 0, ',', '.') }}
                </strong>

                <small>Phim đã thêm</small>
            </div>


            <div class="panel user-stat">
                <span class="user-stat-label">Watchlist</span>

                <strong>
                    {{ number_format($totalWatchlists, 0, ',', '.') }}
                </strong>

                <small>Danh sách đã tạo</small>
            </div>

        </div>


        {{-- FAVORITE GENRES --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Thể loại yêu thích</h3>
                    <p>Dựa trên lịch sử xem phim của người dùng</p>
                </div>
            </div>

            @forelse($favoriteGenres as $genre)

                <div class="genre-stat">

                    <div class="genre-stat-head">
                        <strong>{{ $genre->name }}</strong>

                        <span>
                            {{ number_format($genre->watch_count, 0, ',', '.') }}
                            lượt
                        </span>
                    </div>

                    @php
                        $maxWatchCount = $favoriteGenres->max('watch_count');

                        $percentage = $maxWatchCount > 0
                            ? ($genre->watch_count / $maxWatchCount) * 100
                            : 0;
                    @endphp

                    <div class="genre-bar">
                        <div
                            class="genre-bar-fill"
                            style="width: {{ $percentage }}%;"
                        ></div>
                    </div>

                </div>

            @empty

                <p>Chưa có dữ liệu để xác định thể loại yêu thích.</p>

            @endforelse

        </div>


    @elseif($tab === 'activity')

        {{-- ACTIVITY --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Lịch sử hoạt động</h3>
                    <p>Lịch sử xem phim của người dùng</p>
                </div>
            </div>

            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Tập</th>
                            <th>Thời gian xem</th>
                            <th>Hoạt động cuối</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentActivities as $activity)

                            <tr>

                                <td>
                                    @if($activity->movie)
                                        {{ $activity->movie->title }}
                                    @else
                                        Không xác định
                                    @endif
                                </td>

                                <td>
                                    @if($activity->episode)
                                        Tập {{ $activity->episode->episode_number }}
                                    @else
                                        Phim lẻ
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $watchTime = (int) ($activity->watch_time ?? 0);
                                        $hours = intdiv($watchTime, 3600);
                                        $minutes = intdiv($watchTime % 3600, 60);
                                        $seconds = $watchTime % 60;
                                    @endphp

                                    @if($hours > 0)
                                        {{ $hours }}h {{ $minutes }}m
                                    @elseif($minutes > 0)
                                        {{ $minutes }}m {{ $seconds }}s
                                    @else
                                        {{ $seconds }}s
                                    @endif
                                </td>

                                <td>
                                    @if($activity->last_watched_at)
                                        {{ $activity->last_watched_at->diffForHumans() }}
                                    @elseif($activity->created_at)
                                        {{ $activity->created_at->diffForHumans() }}
                                    @else
                                        —
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    Người dùng chưa có lịch sử xem phim.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($recentActivities, 'hasPages') && $recentActivities->hasPages())
                <div class="pagination">
                    {{ $recentActivities->links('pagination::custom') }}
                </div>
            @endif

        </div>


    @elseif($tab === 'favorites')

        {{-- FAVORITES --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Phim yêu thích</h3>
                    <p>Danh sách phim người dùng đã thêm vào yêu thích</p>
                </div>
            </div>

            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Ngày thêm</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($favorites as $favorite)

                            <tr>

                                <td>
                                    @if($favorite->movie)
                                        {{ $favorite->movie->title }}
                                    @else
                                        Không xác định
                                    @endif
                                </td>

                                <td>
                                    {{ $favorite->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="2" style="text-align:center;">
                                    Người dùng chưa có phim yêu thích.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($favorites, 'hasPages') && $favorites->hasPages())
                <div class="pagination">
                    {{ $favorites->links('pagination::custom') }}
                </div>
            @endif

        </div>


    @elseif($tab === 'comments')

        {{-- COMMENTS --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Comment của User</h3>
                    <p>Quản lý các bình luận do người dùng đăng</p>
                </div>
            </div>

            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($comments as $comment)

                            <tr>

                                <td>
                                    @if($comment->movie)
                                        {{ $comment->movie->title }}
                                    @else
                                        Không xác định
                                    @endif
                                </td>

                                <td>
                                    {{ $comment->content }}
                                </td>

                                <td>
                                    @if($comment->is_approved)
                                        <span class="status">Đã duyệt</span>
                                    @else
                                        <span class="status">Chờ duyệt</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $comment->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    Người dùng chưa có comment.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($comments, 'hasPages') && $comments->hasPages())
                <div class="pagination">
                    {{ $comments->links('pagination::custom') }}
                </div>
            @endif

        </div>


    @elseif($tab === 'ratings')

        {{-- RATINGS --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Rating của User</h3>
                    <p>Danh sách đánh giá phim của người dùng</p>
                </div>
            </div>

            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Rating</th>
                            <th>Ngày đánh giá</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($ratings as $rating)

                            <tr>

                                <td>
                                    @if($rating->movie)
                                        {{ $rating->movie->title }}
                                    @else
                                        Không xác định
                                    @endif
                                </td>

                                <td>
                                    ⭐ {{ $rating->rating }}/10
                                </td>

                                <td>
                                    {{ $rating->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" style="text-align:center;">
                                    Người dùng chưa có rating.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($ratings, 'hasPages') && $ratings->hasPages())
                <div class="pagination">
                    {{ $ratings->links('pagination::custom') }}
                </div>
            @endif

        </div>


    @elseif($tab === 'devices')

        {{-- DEVICES / IP --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Thiết bị / IP</h3>
                    <p>Các thiết bị và địa chỉ IP được ghi nhận từ hoạt động xem phim</p>
                </div>
            </div>

            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Thiết bị / User Agent</th>
                            <th>Số lượt hoạt động</th>
                            <th>Hoạt động cuối</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($devices as $device)

                            <tr>

                                <td>
                                    {{ $device->ip_address ?: 'Không xác định' }}
                                </td>

                                <td style="max-width:420px; word-break:break-word;">
                                    {{ $device->user_agent ?: 'Không xác định' }}
                                </td>

                                <td>
                                    {{ number_format($device->total_views, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if($device->last_seen)
                                        {{ \Carbon\Carbon::parse($device->last_seen)->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    Chưa có dữ liệu thiết bị / IP.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($devices, 'hasPages') && $devices->hasPages())
                <div class="pagination">
                    {{ $devices->links('pagination::custom') }}
                </div>
            @endif

        </div>


    @elseif($tab === 'account')

        {{-- ACCOUNT --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Thông tin tài khoản</h3>
                    <p>Thông tin đăng ký, đăng nhập và trạng thái tài khoản</p>
                </div>
            </div>

            <div class="user-info-grid">

                <div>
                    <span>Email</span>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div>
                    <span>Ngày đăng ký</span>
                    <strong>
                        {{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </strong>
                </div>

                <div>
                    <span>Đăng nhập cuối</span>
                    <strong>
                        @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Chưa đăng nhập
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Trạng thái</span>
                    <strong>
                        @if($user->isBanned())
                            Bị khóa
                        @else
                            Active
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Hoạt động tài khoản</span>
                    <strong>
                        @if(!$user->last_login_at)
                            Chưa từng đăng nhập
                        @elseif($user->last_login_at->diffInDays(now()) >= 90)
                            Không hoạt động trên 90 ngày
                        @elseif($user->last_login_at->diffInDays(now()) >= 30)
                            Không hoạt động trên 30 ngày
                        @elseif($user->last_login_at->diffInDays(now()) >= 7)
                            Không hoạt động trên 7 ngày
                        @else
                            Đang hoạt động
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Role</span>
                    <strong>{{ ucfirst($user->role) }}</strong>
                </div>

                @if($user->isBanned())

                    <div>
                        <span>Lý do khóa</span>
                        <strong>{{ $user->ban_reason ?: 'Không có lý do' }}</strong>
                    </div>

                    <div>
                        <span>Hết hạn khóa</span>
                        <strong>
                            @if($user->ban_expires_at)
                                {{ $user->ban_expires_at->format('d/m/Y H:i') }}
                            @else
                                Vĩnh viễn
                            @endif
                        </strong>
                    </div>

                @endif

            </div>

        </div>


    @elseif($tab === 'notifications')

        {{-- NOTIFICATIONS --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h3>Thông báo</h3>
                    <p>Lịch sử thông báo và gửi thông báo cho User</p>
                </div>
            </div>


            {{-- SEND NOTIFICATION UI --}}
            <div class="panel" style="margin-bottom:20px;">

                <h4>Gửi thông báo mới</h4>

                <div class="user-info-grid">

                    <div>
                        <span>User</span>
                        <strong>{{ $user->name }}</strong>
                    </div>

                    <div>
                        <span>Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>

                </div>

            </div>


            {{-- NOTIFICATION HISTORY --}}
            <div class="table-responsive">
                <table>

                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($notifications as $notification)

                            <tr>

                                <td>
                                    {{ $notification->title }}
                                </td>

                                <td>
                                    {{ $notification->message }}
                                </td>

                                <td>
                                    {{ $notification->type ?: 'system' }}
                                </td>

                                <td>
                                    @if($notification->read_at)
                                        Đã đọc
                                    @else
                                        Chưa đọc
                                    @endif
                                </td>

                                <td>
                                    {{ $notification->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" style="text-align:center;">
                                    User chưa có thông báo.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
                <div class="pagination">
                    {{ $notifications->links('pagination::custom') }}
                </div>
            @endif

        </div>

    @endif
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

</section>


{{-- TAB CSS --}}
<style>
.user-detail-tabs {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 16px;
    margin-bottom: 16px;
    padding: 8px;
}

.user-detail-tabs a {
    display: inline-flex;
    align-items: center;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    opacity: .7;
}

.user-detail-tabs a:hover {
    opacity: 1;
}

.user-detail-tabs a.active {
    opacity: 1;
    background: rgba(255,255,255,.08);
}

.user-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 16px;
    margin-bottom: 16px;
}

.user-stat {
    padding: 20px;
}

.user-stat-label {
    display: block;
    margin-bottom: 8px;
    opacity: .7;
}

.user-stat strong {
    display: block;
    font-size: 22px;
}

.user-stat small {
    display: block;
    margin-top: 6px;
    opacity: .6;
}

.genre-stat {
    margin-bottom: 18px;
}

.genre-stat-head {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.genre-stat-head span {
    opacity: .7;
}

.genre-bar {
    width: 100%;
    height: 8px;
    border-radius: 4px;
    background: rgba(255,255,255,.08);
    overflow: hidden;
}

.genre-bar-fill {
    height: 100%;
    border-radius: 4px;
    background: currentColor;
}

.user-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.user-info-grid div span {
    display: block;
    margin-bottom: 5px;
    opacity: .6;
}

.user-info-grid div strong {
    display: block;
    word-break: break-word;
}

.pagination {
    margin-top: 20px;
}

@media (max-width: 1100px) {
    .user-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    .user-detail-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
    }

    .user-detail-tabs a {
        white-space: nowrap;
    }

    .user-stats {
        grid-template-columns: 1fr;
    }

    .user-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
