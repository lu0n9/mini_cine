@extends('admin.layouts.master')

@section('title', 'Quản lý bình luận Forum')

@section('content')

<div class="admin-page">

    <div class="admin-page-header">

        <div>
            <span class="admin-page-eyebrow">
                FORUM
            </span>

            <h1>Bình luận</h1>

            <p>
                Kiểm duyệt và quản lý bình luận của thành viên.
            </p>
        </div>

    </div>


    <form
        method="GET"
        action="{{ route('admin.forum.comments') }}"
        class="forum-filter"
    >

        <input
            type="text"
            name="keyword"
            value="{{ request('keyword') }}"
            placeholder="Tìm nội dung bình luận..."
        >


        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>

            <option
                value="pending"
                @selected(request('status') === 'pending')
            >
                Chờ duyệt
            </option>

            <option
                value="approved"
                @selected(request('status') === 'approved')
            >
                Đã duyệt
            </option>

            <option
                value="hidden"
                @selected(request('status') === 'hidden')
            >
                Đã ẩn
            </option>

        </select>


        <button type="submit">
            Lọc
        </button>

    </form>


    <div class="admin-table-card">

        <div class="admin-table-wrap">

            <table class="admin-table">

                <thead>

                    <tr>
                        <th>Người dùng</th>
                        <th>Nội dung</th>
                        <th>Bài viết</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody>

                @forelse($comments as $comment)

                    <tr>

                        <td>
                            <strong>
                                {{ $comment->user->game_name
                                    ?? $comment->user->name }}
                            </strong>
                        </td>


                        <td>

                            <div class="comment-preview">
                                {{ Str::limit(
                                    $comment->content,
                                    180
                                ) }}
                            </div>

                        </td>


                        <td>

                            @if($comment->post)

                                <span class="table-category">
                                    {{ Str::limit(
                                        $comment->post->title,
                                        35
                                    ) }}
                                </span>

                            @else
                                —
                            @endif

                        </td>


                        <td>

                            @if($comment->status === 'approved')

                                <span class="status approved">
                                    Đã duyệt
                                </span>

                            @elseif($comment->status === 'pending')

                                <span class="status pending">
                                    Chờ duyệt
                                </span>

                            @else

                                <span class="status hidden">
                                    Đã ẩn
                                </span>

                            @endif

                        </td>


                        <td>
                            {{ $comment->created_at->diffForHumans() }}
                        </td>


                        <td>

                            <div class="table-actions">

                                @if($comment->status !== 'approved')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.forum.comments.status',
                                            $comment
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="approved"
                                        >

                                        <button
                                            type="submit"
                                            title="Duyệt"
                                        >
                                            ✓
                                        </button>

                                    </form>

                                @endif


                                @if($comment->status !== 'hidden')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.forum.comments.status',
                                            $comment
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="hidden"
                                        >

                                        <button
                                            type="submit"
                                            title="Ẩn"
                                        >
                                            ◉
                                        </button>

                                    </form>

                                @endif


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.forum.comments.destroy',
                                        $comment
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Bạn có chắc muốn xóa bình luận này?'
                                    )"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        title="Xóa"
                                    >
                                        ✕
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6">

                            <div class="table-empty">
                                Không có bình luận nào.
                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($comments->hasPages())

            <div class="pagination">
                {{ $comments->links('pagination::custom') }}
            </div>
        @endif

    </div>

</div>

@endsection