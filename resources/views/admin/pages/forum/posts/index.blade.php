@extends('admin.layouts.master')

@section('title', 'Quản lý bài viết Forum')

@section('content')

<div class="admin-page">

    <div class="admin-page-header">

        <div>
            <span class="admin-page-eyebrow">
                FORUM
            </span>

            <h1>Bài viết</h1>

            <p>
                Quản lý và kiểm duyệt bài viết trong cộng đồng.
            </p>
        </div>

    </div>


    {{-- Filters --}}
    <form
        method="GET"
        action="{{ route('admin.forum.posts') }}"
        class="forum-filter"
    >

        <input
            type="text"
            name="keyword"
            value="{{ request('keyword') }}"
            placeholder="Tìm kiếm bài viết..."
        >

        <select name="status">

            <option value="">
                Tất cả trạng thái
            </option>

            <option
                value="published"
                @selected(request('status') === 'published')
            >
                Published
            </option>

            <option
                value="draft"
                @selected(request('status') === 'draft')
            >
                Draft
            </option>

            <option
                value="hidden"
                @selected(request('status') === 'hidden')
            >
                Hidden
            </option>

        </select>


        <select name="category_id">

            <option value="">
                Tất cả danh mục
            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(
                        (string) request('category_id')
                        === (string) $category->id
                    )
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>


        <button type="submit">
            Lọc
        </button>

        @if(request()->hasAny([
            'keyword',
            'status',
            'category_id'
        ]))
            <a href="{{ route('admin.forum.posts') }}">
                Xóa lọc
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="admin-table-card">

        <div class="admin-table-wrap">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Bài viết</th>
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Thống kê</th>
                        <th>Ngày đăng</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($posts as $post)

                    <tr>

                        <td>

                            <div class="forum-post-admin-title">

                                @if($post->is_pinned)
                                    <span title="Đã ghim">📌</span>
                                @endif

                                <div>
                                    <strong>
                                        {{ $post->title }}
                                    </strong>

                                    @if($post->is_locked)
                                        <small>🔒 Đã khóa</small>
                                    @endif
                                </div>

                            </div>

                        </td>


                        <td>
                            <span class="table-category">
                                {{ $post->category->name }}
                            </span>
                        </td>


                        <td>

                            {{ $post->user->game_name
                                ?? $post->user->name }}

                        </td>


                        <td>

                            @if($post->status === 'published')

                                <span class="status approved">
                                    Published
                                </span>

                            @elseif($post->status === 'draft')

                                <span class="status pending">
                                    Draft
                                </span>

                            @else

                                <span class="status hidden">
                                    Hidden
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="post-stats">
                                💬 {{ $post->comments_count }}
                                ·
                                👁 {{ $post->views_count }}
                            </div>

                        </td>


                        <td>
                            {{ $post->created_at->format('d/m/Y H:i') }}
                        </td>


                        <td>

                            <div class="table-actions">

                                <a
                                    href="{{ route(
                                        'admin.forum.posts.edit',
                                        $post
                                    ) }}"
                                    title="Sửa"
                                >
                                    ✎
                                </a>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.forum.posts.pin',
                                        $post
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        title="{{ $post->is_pinned
                                            ? 'Bỏ ghim'
                                            : 'Ghim' }}"
                                    >
                                        {{ $post->is_pinned ? '📌' : '☆' }}
                                    </button>

                                </form>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.forum.posts.lock',
                                        $post
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        title="{{ $post->is_locked
                                            ? 'Mở khóa'
                                            : 'Khóa' }}"
                                    >
                                        {{ $post->is_locked ? '🔓' : '🔒' }}
                                    </button>

                                </form>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.forum.posts.destroy',
                                        $post
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Bạn có chắc muốn xóa bài viết này?'
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
                        <td colspan="7">
                            <div class="table-empty">
                                Không tìm thấy bài viết.
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($posts->hasPages())

            <div class="pagination">
                {{ $posts->links('pagination::custom') }}
            </div>

        @endif

    </div>

</div>

@endsection