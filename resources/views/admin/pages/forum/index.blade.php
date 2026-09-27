@extends('admin.layouts.master')

@section('title', 'Quản lý Forum')

@section('content')

<div class="forum-admin">

    {{-- Header --}}
    <div class="forum-admin-header">

        <div>
            <div class="forum-admin-eyebrow">
                COMMUNITY
            </div>

            <h1>Quản lý Forum</h1>

            <p>
                Quản lý danh mục, bài viết và bình luận của cộng đồng Mini Cine.
            </p>
        </div>

    </div>


    {{-- Stats --}}
    <div class="forum-admin-stats">

        <a href="{{ route('admin.forum.categories') }}"
           class="forum-admin-stat">

            <span class="forum-admin-stat-icon">◈</span>

            <div>
                <strong>{{ $stats['categories'] }}</strong>
                <small>Danh mục</small>
            </div>

        </a>


        <a href="{{ route('admin.forum.posts') }}"
           class="forum-admin-stat">

            <span class="forum-admin-stat-icon">▤</span>

            <div>
                <strong>{{ $stats['posts'] }}</strong>
                <small>Tổng bài viết</small>
            </div>

        </a>


        <div class="forum-admin-stat">

            <span class="forum-admin-stat-icon">●</span>

            <div>
                <strong>{{ $stats['published_posts'] }}</strong>
                <small>Đã xuất bản</small>
            </div>

        </div>


        <a href="{{ route('admin.forum.comments') }}"
           class="forum-admin-stat">

            <span class="forum-admin-stat-icon">◌</span>

            <div>
                <strong>{{ $stats['comments'] }}</strong>
                <small>Bình luận</small>
            </div>

        </a>


        <a href="{{ route('admin.forum.comments', ['status' => 'pending']) }}"
           class="forum-admin-stat warning">

            <span class="forum-admin-stat-icon">!</span>

            <div>
                <strong>{{ $stats['pending_comments'] }}</strong>
                <small>Chờ duyệt</small>
            </div>

        </a>

    </div>


    {{-- Quick actions --}}
    <div class="forum-admin-grid">

        <section class="forum-admin-card">

            <div class="forum-admin-card-header">

                <div>
                    <span class="forum-admin-card-label">
                        QUẢN LÝ
                    </span>

                    <h2>Forum</h2>
                </div>

            </div>


            <div class="forum-admin-links">

                <a href="{{ route('admin.forum.categories') }}">
                    <span>◈</span>
                    <div>
                        <strong>Danh mục</strong>
                        <small>
                            Quản lý các chuyên mục forum
                        </small>
                    </div>
                    <b>›</b>
                </a>


                <a href="{{ route('admin.forum.posts') }}">
                    <span>▤</span>
                    <div>
                        <strong>Bài viết</strong>
                        <small>
                            Kiểm duyệt và quản lý bài viết
                        </small>
                    </div>
                    <b>›</b>
                </a>


                <a href="{{ route('admin.forum.comments') }}">
                    <span>◌</span>
                    <div>
                        <strong>Bình luận</strong>
                        <small>
                            Duyệt và xử lý bình luận
                        </small>
                    </div>
                    <b>›</b>
                </a>

            </div>

        </section>


        {{-- Latest posts --}}
        <section class="forum-admin-card">

            <div class="forum-admin-card-header">

                <div>
                    <span class="forum-admin-card-label">
                        RECENT
                    </span>

                    <h2>Bài viết mới</h2>
                </div>

                <a href="{{ route('admin.forum.posts') }}">
                    Xem tất cả
                </a>

            </div>


            <div class="forum-admin-list">

                @forelse($latestPosts as $post)

                    <a
                        href="{{ route('admin.forum.posts.edit', $post) }}"
                        class="forum-admin-list-item"
                    >

                        <div class="forum-admin-avatar">
                            {{ strtoupper(substr(
                                $post->user->game_name
                                ?? $post->user->name
                                ?? 'U',
                                0,
                                1
                            )) }}
                        </div>

                        <div class="forum-admin-list-content">

                            <strong>
                                {{ $post->title }}
                            </strong>

                            <span>
                                {{ $post->category->name }}
                                ·
                                {{ $post->created_at->diffForHumans() }}
                            </span>

                        </div>

                        @if($post->is_pinned)
                            <span class="forum-admin-pin">
                                📌
                            </span>
                        @endif

                    </a>

                @empty

                    <div class="forum-admin-empty">
                        Chưa có bài viết.
                    </div>

                @endforelse

            </div>

        </section>

    </div>


    {{-- Latest comments --}}
    <section class="forum-admin-card">

        <div class="forum-admin-card-header">

            <div>
                <span class="forum-admin-card-label">
                    MODERATION
                </span>

                <h2>Bình luận gần đây</h2>
            </div>

            <a href="{{ route('admin.forum.comments') }}">
                Xem tất cả
            </a>

        </div>


        <div class="forum-admin-comment-list">

            @forelse($latestComments as $comment)

                <div class="forum-admin-comment">

                    <div class="forum-admin-avatar">
                        {{ strtoupper(substr(
                            $comment->user->game_name
                            ?? $comment->user->name
                            ?? 'U',
                            0,
                            1
                        )) }}
                    </div>

                    <div class="forum-admin-comment-body">

                        <div class="forum-admin-comment-top">

                            <strong>
                                {{ $comment->user->game_name
                                    ?? $comment->user->name }}
                            </strong>

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

                        </div>

                        <p>
                            {{ Str::limit($comment->content, 180) }}
                        </p>

                        <small>
                            {{ $comment->post?->title }}
                            ·
                            {{ $comment->created_at->diffForHumans() }}
                        </small>

                    </div>

                </div>

            @empty

                <div class="forum-admin-empty">
                    Chưa có bình luận.
                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection