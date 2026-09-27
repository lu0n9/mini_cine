@extends('client.layouts.master')

@section('title', $category->name . ' - Forum Mini Cine')

@section('content')

<div class="forum-page">

    <div class="forum-container">

        <div class="forum-breadcrumb">
            <a href="{{ route('forum.index') }}">
                Forum
            </a>

            <span>›</span>

            <span>{{ $category->name }}</span>
        </div>


        <div class="forum-header">

            <div>

                <div class="forum-category-heading">

                    <span class="forum-category-heading-icon">
                        {{ $category->icon ?: '💬' }}
                    </span>

                    <div>

                        <h1>
                            {{ $category->name }}
                        </h1>

                        @if($category->description)
                            <p>
                                {{ $category->description }}
                            </p>
                        @endif

                    </div>

                </div>

            </div>

            @auth

                <a
                    href="{{ route('forum.create') }}"
                    class="forum-create-btn"
                >
                    + Viết bài
                </a>

            @endauth

        </div>


        <div class="forum-post-list">

            @forelse($posts as $post)

                <article class="forum-post-card">

                    <div class="forum-post-main">

                        @if($post->is_pinned)
                            <span class="forum-pinned">
                                📌 Ghim
                            </span>
                        @endif

                        <h3>
                            <a href="{{ route('forum.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <div class="forum-post-meta">

                            <span>
                                {{ $post->user->game_name ?? $post->user->name }}
                            </span>

                            <span>·</span>

                            <span>
                                {{ $post->created_at->diffForHumans() }}
                            </span>

                        </div>

                    </div>

                    <div class="forum-post-stats">

                        <span>
                            💬 {{ $post->comments_count }}
                        </span>

                        <span>
                            ❤️ {{ $post->likes_count }}
                        </span>

                        <span>
                            👁 {{ $post->views_count }}
                        </span>

                    </div>

                </article>

            @empty

                <div class="forum-empty">
                    Danh mục này chưa có bài viết.
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