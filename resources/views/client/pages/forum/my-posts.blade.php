@extends('client.layouts.master')

@section('title', 'Bài viết của tôi - Forum Mini Cine')

@section('content')

<div class="forum-page">

    <div class="forum-container">

        <div class="forum-page-header">

            <div>

                <span class="forum-page-eyebrow">
                    MY FORUM
                </span>

                <h1>Bài viết của tôi</h1>

                <p>
                    Quản lý những bài viết bạn đã đăng trong cộng đồng Mini Cine.
                </p>

            </div>

            <a
                href="{{ route('forum.create') }}"
                class="forum-primary-btn"
            >
                + Viết bài
            </a>

        </div>


        <div class="forum-my-posts">

            @forelse($posts as $post)

                <article class="forum-my-post">

                    <div class="forum-my-post-main">

                        <div class="forum-my-post-top">

                            @if($post->is_pinned)
                                <span class="forum-topic-pinned">
                                    📌 Ghim
                                </span>
                            @endif

                            <a
                                href="{{ route(
                                    'forum.category',
                                    $post->category->slug
                                ) }}"
                                class="forum-topic-category"
                            >
                                {{ $post->category->name }}
                            </a>

                        </div>


                        <h2>

                            <a
                                href="{{ route(
                                    'forum.show',
                                    $post->slug
                                ) }}"
                            >
                                {{ $post->title }}
                            </a>

                        </h2>


                        <div class="forum-my-post-meta">

                            <span>
                                {{ $post->created_at->diffForHumans() }}
                            </span>

                            <span>·</span>

                            <span>
                                💬 {{ $post->comments_count }}
                            </span>

                            <span>·</span>

                            <span>
                                👁 {{ $post->views_count }}
                            </span>

                        </div>

                    </div>


                    <div class="forum-my-post-status">

                        @if($post->status === 'published')

                            <span class="status published">
                                Published
                            </span>

                        @elseif($post->status === 'draft')

                            <span class="status draft">
                                Draft
                            </span>

                        @else

                            <span class="status hidden">
                                Hidden
                            </span>

                        @endif

                    </div>


                    <div class="forum-my-post-actions">

                        <a
                            href="{{ route(
                                'forum.edit',
                                $post
                            ) }}"
                        >
                            Sửa
                        </a>

                        <form
                            method="POST"
                            action="{{ route(
                                'forum.destroy',
                                $post
                            ) }}"
                            onsubmit="return confirm(
                                'Bạn có chắc muốn xóa bài viết này?'
                            )"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">
                                Xóa
                            </button>

                        </form>

                    </div>

                </article>

            @empty

                <div class="forum-empty">

                    <div class="forum-empty-icon">
                        📝
                    </div>

                    <h3>
                        Bạn chưa có bài viết nào
                    </h3>

                    <p>
                        Hãy bắt đầu cuộc thảo luận đầu tiên của bạn.
                    </p>

                    <a
                        href="{{ route('forum.create') }}"
                        class="forum-primary-btn"
                    >
                        + Viết bài đầu tiên
                    </a>

                </div>

            @endforelse

        </div>


        @if($posts->hasPages())

            <div class="forum-pagination">
                {{ $posts->links() }}
            </div>

        @endif

    </div>

</div>

@endsection