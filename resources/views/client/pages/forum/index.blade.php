@extends('client.layouts.master')

@section('title', 'Forum - Mini Cine')

@section('content')

<div class="forum-page">

    <div class="forum-container">


        {{-- =====================================================
            HERO
        ====================================================== --}}

        <header class="forum-hero">

            <div class="forum-hero-content">

                <div class="forum-hero-badge">
                    <span class="forum-hero-dot"></span>
                    CỘNG ĐỒNG MINI CINE
                </div>

                <h1>
                    Forum Mini Cine
                </h1>

                <p>
                    Nơi chia sẻ cảm nhận, thảo luận và khám phá
                    những bộ phim bạn yêu thích.
                </p>

            </div>


            <div class="forum-hero-actions">

                @auth

                    <a
                        href="{{ route('forum.create') }}"
                        class="forum-primary-btn"
                    >
                        <span>+</span>
                        Viết bài
                    </a>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="forum-primary-btn"
                    >
                        <span>+</span>
                        Viết bài
                    </a>

                @endauth

            </div>

        </header>



        {{-- =====================================================
            CONTENT GRID
        ====================================================== --}}

        <div class="forum-layout">


            {{-- =================================================
                LEFT SIDEBAR
            ================================================== --}}

            <aside class="forum-sidebar">


                {{-- Community overview --}}
                <section class="forum-side-card forum-community-card">

                    <div class="forum-side-title">
                        <span>Cộng đồng</span>
                    </div>


                    <div class="forum-community-stats">

                        <div class="forum-community-stat">

                            <strong>
                                {{ $categories->count() }}
                            </strong>

                            <span>
                                Danh mục
                            </span>

                        </div>


                        <div class="forum-community-divider"></div>


                        <div class="forum-community-stat">

                            <strong>
                                {{ $categories->sum('posts_count') }}
                            </strong>

                            <span>
                                Bài viết
                            </span>

                        </div>

                    </div>

                </section>



                {{-- Categories --}}
                <section class="forum-side-card">

                    <div class="forum-side-title">

                        <span>
                            Danh mục
                        </span>

                        <span class="forum-side-count">
                            {{ $categories->count() }}
                        </span>

                    </div>


                    <nav class="forum-category-nav">

                        @foreach($categories as $category)

                            <a
                                href="{{ route('forum.category', $category->slug) }}"
                                class="forum-category-nav-item"
                            >

                                <span class="forum-category-nav-icon">
                                    {{ $category->icon ?: '💬' }}
                                </span>


                                <span class="forum-category-nav-info">

                                    <strong>
                                        {{ $category->name }}
                                    </strong>

                                    <small>
                                        {{ $category->posts_count }} bài viết
                                    </small>

                                </span>


                                <span class="forum-category-nav-arrow">
                                    ›
                                </span>

                            </a>

                        @endforeach

                    </nav>

                </section>



                {{-- Community rules --}}
                <section class="forum-side-card forum-rules-card">

                    <div class="forum-side-title">
                        <span>Tham gia cộng đồng</span>
                    </div>

                    <p>
                        Chia sẻ những điều bạn yêu thích,
                        tôn trọng thành viên khác và cùng xây dựng
                        cộng đồng Mini Cine.
                    </p>

                </section>

            </aside>



            {{-- =================================================
                MAIN
            ================================================== --}}

            <main class="forum-main">


                {{-- Latest header --}}
                <div class="forum-main-header">

                    <div>

                        <span class="forum-main-eyebrow">
                            DISCUSSIONS
                        </span>

                        <h2>
                            Bài viết mới nhất
                        </h2>

                    </div>


                    <div class="forum-topic-total">

                        {{ $posts->total() }}

                        <span>
                            bài viết
                        </span>

                    </div>

                </div>



                {{-- Filter bar --}}
                <div class="forum-toolbar">

                    <div class="forum-toolbar-tabs">

                        <span class="forum-toolbar-tab active">
                            Mới nhất
                        </span>

                    </div>


                    @auth

                        <a
                            href="{{ route('forum.create') }}"
                            class="forum-toolbar-create"
                        >
                            + Tạo chủ đề
                        </a>

                    @endauth

                </div>



                {{-- Posts --}}
                <div class="forum-topic-list">

                    @forelse($posts as $post)

                        <article
                            class="forum-topic"
                            @if($post->is_pinned)
                                data-pinned="true"
                            @endif
                        >


                            {{-- Author --}}
                            <div class="forum-topic-avatar">

                                @if($post->user->avatar)

                                    <img
                                        src="{{ asset($post->user->avatar) }}"
                                        alt="{{ $post->user->game_name ?? $post->user->name }}"
                                    >

                                @else

                                    <span>
                                        {{
                                            strtoupper(
                                                substr(
                                                    $post->user->game_name
                                                    ?? $post->user->name
                                                    ?? 'U',
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    </span>

                                @endif

                            </div>



                            {{-- Main --}}
                            <div class="forum-topic-content">


                                <div class="forum-topic-top">

                                    @if($post->is_pinned)

                                        <span class="forum-topic-pinned">
                                            📌 Ghim
                                        </span>

                                    @endif


                                    <a
                                        href="{{ route('forum.category', $post->category->slug) }}"
                                        class="forum-topic-category"
                                    >
                                        {{ $post->category->name }}
                                    </a>

                                </div>


                                <h3>

                                    <a
                                        href="{{ route('forum.show', $post->slug) }}"
                                    >
                                        {{ $post->title }}
                                    </a>

                                </h3>


                                <div class="forum-topic-meta">

                                    <span>
                                        {{ $post->user->game_name ?? $post->user->name }}
                                    </span>

                                    <span class="forum-meta-dot">
                                        ·
                                    </span>

                                    <time>
                                        {{ $post->created_at->diffForHumans() }}
                                    </time>

                                </div>

                            </div>



                            {{-- Stats --}}
                            <div class="forum-topic-stats">


                                <div class="forum-topic-stat">

                                    <strong>
                                        {{ $post->comments_count }}
                                    </strong>

                                    <span>
                                        trả lời
                                    </span>

                                </div>


                                <div class="forum-topic-stat">

                                    <strong>
                                        {{ $post->views_count }}
                                    </strong>

                                    <span>
                                        lượt xem
                                    </span>

                                </div>


                            </div>


                        </article>

                    @empty


                        <div class="forum-empty">

                            <div class="forum-empty-icon">
                                💬
                            </div>

                            <h3>
                                Chưa có bài viết
                            </h3>

                            <p>
                                Hãy trở thành người đầu tiên bắt đầu cuộc trò chuyện.
                            </p>

                            @auth

                                <a
                                    href="{{ route('forum.create') }}"
                                    class="forum-primary-btn"
                                >
                                    + Viết bài đầu tiên
                                </a>

                            @endauth

                        </div>


                    @endforelse

                </div>



                {{-- Pagination --}}
                @if($posts->hasPages())

                    <div class="forum-pagination">
                        {{ $posts->links() }}
                    </div>

                @endif

            </main>

        </div>

    </div>

</div>

@endsection