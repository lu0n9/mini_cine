@extends('admin.layouts.master')

@section('title', 'Danh mục Forum')

@section('content')

<div class="forum-admin-page">

    {{-- Header --}}
    <div class="forum-admin-page-header">

        <div>
            <span class="forum-admin-eyebrow">
                FORUM / CATEGORIES
            </span>

            <h1>Danh mục Forum</h1>

            <p>
                Quản lý các danh mục được sử dụng trong cộng đồng Mini Cine.
            </p>
        </div>

        <a
            href="{{ route('admin.forum.categories.create') }}"
            class="forum-admin-primary-btn"
        >
            <span>+</span>
            Thêm danh mục
        </a>

    </div>


    {{-- Flash message --}}
    @if(session('success'))

        <div class="forum-admin-alert success">
            <span>✓</span>
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="forum-admin-alert error">
            <span>!</span>
            {{ session('error') }}
        </div>

    @endif


    {{-- Category table --}}
    <div class="forum-admin-panel">

        <div class="forum-admin-panel-header">

            <div>
                <span class="forum-admin-panel-label">
                    CATEGORIES
                </span>

                <h2>
                    Danh sách danh mục
                </h2>
            </div>

            <div class="forum-admin-panel-count">
                {{ $categories->total() }} danh mục
            </div>

        </div>


        <div class="forum-category-table-wrap">

            <table class="forum-category-table">

                <thead>

                    <tr>
                        <th style="width: 55px;">#</th>
                        <th>Danh mục</th>
                        <th>Slug</th>
                        <th>Bài viết</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th style="width: 150px;">Thao tác</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($categories as $category)

                    <tr>

                        {{-- ID --}}
                        <td>
                            <span class="forum-category-id">
                                #{{ $category->id }}
                            </span>
                        </td>


                        {{-- Category --}}
                        <td>

                            <div class="forum-category-info">

                                <div class="forum-category-icon">
                                    {{ $category->icon ?: '💬' }}
                                </div>

                                <div class="forum-category-content">

                                    <strong>
                                        {{ $category->name }}
                                    </strong>

                                    @if($category->description)

                                        <small>
                                            {{ Str::limit(
                                                $category->description,
                                                70
                                            ) }}
                                        </small>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- Slug --}}
                        <td>

                            <code class="forum-category-slug">
                                /{{ $category->slug }}
                            </code>

                        </td>


                        {{-- Posts --}}
                        <td>

                            <span class="forum-category-post-count">
                                {{ $category->posts_count }}
                            </span>

                        </td>


                        {{-- Sort --}}
                        <td>

                            <span class="forum-category-sort">
                                {{ $category->sort_order }}
                            </span>

                        </td>


                        {{-- Status --}}
                        <td>

                            @if($category->is_active)

                                <span class="forum-category-status active">
                                    <i></i>
                                    Đang hoạt động
                                </span>

                            @else

                                <span class="forum-category-status inactive">
                                    <i></i>
                                    Đã tắt
                                </span>

                            @endif

                        </td>


                        {{-- Actions --}}
                        <td>

                            <div class="forum-category-actions">

                                {{-- Edit --}}
                                <a
                                    href="{{ route(
                                        'admin.forum.categories.edit',
                                        $category
                                    ) }}"
                                    class="forum-category-action edit"
                                    title="Chỉnh sửa"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                    </svg>
                                </a>


                                {{-- Toggle --}}
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.forum.categories.toggle',
                                        $category
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="forum-category-action toggle"
                                        title="{{ $category->is_active
                                            ? 'Tắt danh mục'
                                            : 'Bật danh mục' }}"
                                    >
                                        @if($category->is_active)

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M12 2v10"/>
                                                <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                            </svg>

                                        @else

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="9"
                                                />
                                            </svg>

                                        @endif
                                    </button>

                                </form>


                                {{-- Delete --}}
                                @if($category->posts_count === 0)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.forum.categories.destroy',
                                            $category
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn xóa danh mục này?'
                                        )"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="forum-category-action delete"
                                            title="Xóa"
                                        >
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="M19 6l-1 14H6L5 6"/>
                                                <path d="M10 11v5M14 11v5"/>
                                            </svg>
                                        </button>

                                    </form>

                                @else

                                    <button
                                        type="button"
                                        class="forum-category-action delete disabled"
                                        title="Không thể xóa vì danh mục đang có bài viết"
                                        disabled
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4h8v2"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v5M14 11v5"/>
                                        </svg>
                                    </button>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="forum-category-empty"
                        >

                            <div class="forum-category-empty-icon">
                                ◈
                            </div>

                            <strong>
                                Chưa có danh mục
                            </strong>

                            <span>
                                Hãy tạo danh mục đầu tiên cho Forum.
                            </span>

                            <a
                                href="{{ route(
                                    'admin.forum.categories.create'
                                ) }}"
                                class="forum-admin-primary-btn"
                            >
                                + Thêm danh mục
                            </a>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($categories->hasPages())

            <div class="pagination">
                {{ $categories->links('pagination::custom') }}
            </div>

        @endif

    </div>

</div>

@endsection