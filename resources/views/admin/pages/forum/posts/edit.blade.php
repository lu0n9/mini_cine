@extends('admin.layouts.master')

@section('title', 'Sửa bài viết Forum')

@section('content')

<div class="admin-page">

    <div class="admin-page-header">
        <div>
            <span class="admin-page-eyebrow">
                FORUM / POSTS
            </span>

            <h1>Sửa bài viết</h1>

            <p>
                Chỉnh sửa nội dung và thông tin bài viết trong cộng đồng.
            </p>
        </div>
    </div>


    <form
        method="POST"
        action="{{ route('admin.forum.posts.update', $post) }}"
    >

        @csrf
        @method('PUT')


        <div class="admin-form-card">

            <div class="admin-form-body">

                <div class="admin-form-grid">

                    {{-- Main --}}

                    <div class="admin-form-main">

                        {{-- Title --}}

                        <div class="admin-form-group">

                            <label class="admin-form-label">
                                Tiêu đề
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                value="{{ old('title', $post->title) }}"
                                class="admin-form-control @error('title') is-invalid @enderror"
                                placeholder="Nhập tiêu đề bài viết..."
                            >

                            @error('title')
                                <div class="admin-form-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Category --}}

                        <div class="admin-form-group">

                            <label class="admin-form-label">
                                Danh mục
                                <span class="required">*</span>
                            </label>

                            <select
                                name="category_id"
                                class="admin-form-control @error('category_id') is-invalid @enderror"
                            >

                                <option value="">
                                    Chọn danh mục
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            old(
                                                'category_id',
                                                $post->category_id
                                            ) == $category->id
                                        )
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('category_id')
                                <div class="admin-form-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Content --}}

                        <div class="admin-form-group admin-post-content">

                            <label class="admin-form-label">
                                Nội dung
                                <span class="required">*</span>
                            </label>

                            <textarea
                                name="content"
                                class="admin-form-control @error('content') is-invalid @enderror"
                                placeholder="Nhập nội dung bài viết..."
                            >{{ old('content', $post->content) }}</textarea>

                            @error('content')
                                <div class="admin-form-error">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    {{-- Sidebar --}}

                    <aside class="admin-form-sidebar">

                        <div class="admin-form-side-card">

                            <h3 class="admin-form-side-title">
                                Thông tin bài viết
                            </h3>

                            <p>
                                Tác giả:
                                <strong>
                                    {{ $post->user->game_name ?? $post->user->name }}
                                </strong>
                            </p>

                            <p>
                                Ngày đăng:
                                {{ $post->created_at->format('d/m/Y H:i') }}
                            </p>

                            <p>
                                Lượt xem:
                                {{ $post->views_count }}
                            </p>

                        </div>


                        <div class="admin-form-side-card">

                            <h3 class="admin-form-side-title">
                                Trạng thái
                            </h3>

                            <label class="admin-form-check">

                                <input
                                    type="checkbox"
                                    name="is_pinned"
                                    value="1"
                                    @checked(
                                        old(
                                            'is_pinned',
                                            $post->is_pinned
                                        )
                                    )
                                >

                                <span>
                                    Ghim bài viết
                                </span>

                            </label>


                            <label class="admin-form-check">

                                <input
                                    type="checkbox"
                                    name="is_locked"
                                    value="1"
                                    @checked(
                                        old(
                                            'is_locked',
                                            $post->is_locked
                                        )
                                    )
                                >

                                <span>
                                    Khóa bình luận
                                </span>

                            </label>

                        </div>

                    </aside>

                </div>


                {{-- Actions --}}

                <div class="admin-form-actions">

                    <div class="admin-form-actions-left">

                        <a
                            href="{{ route('admin.forum.posts') }}"
                            class="admin-btn admin-btn-secondary"
                        >
                            Quay lại
                        </a>

                    </div>


                    <div class="admin-form-actions-right">

                        <button
                            type="submit"
                            class="admin-btn admin-btn-primary"
                        >
                            Lưu thay đổi
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection