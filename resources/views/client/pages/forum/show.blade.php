@extends('client.layouts.master')

@section('title', $post->title . ' - Forum Mini Cine')

@section('content')

<div class="forum-page forum-show-page">

    <div class="forum-container">

        {{-- BACK --}}
        <div class="forum-show-back">
            <a href="{{ route('forum.index') }}">
                <span>‹</span>
                Forum Mini Cine
            </a>

            <span class="forum-show-back-separator">/</span>

            <a href="{{ route('forum.category', $post->category->slug) }}">
                {{ $post->category->name }}
            </a>
        </div>


        <div class="forum-show-layout">

            {{-- =====================================================
                MAIN
            ====================================================== --}}
            <main class="forum-show-main">

                {{-- POST --}}
                <article class="forum-post">

                    {{-- POST HEADER --}}
                    <header class="forum-post-header">

                        <div class="forum-post-badges">

                            @if($post->is_pinned)
                                <span class="forum-show-badge pinned">
                                    📌 Ghim
                                </span>
                            @endif

                            @if($post->is_locked)
                                <span class="forum-show-badge locked">
                                    🔒 Đã khóa
                                </span>
                            @endif

                            <a
                                href="{{ route('forum.category', $post->category->slug) }}"
                                class="forum-show-category"
                            >
                                {{ $post->category->icon ?: '💬' }}
                                {{ $post->category->name }}
                            </a>

                        </div>


                        <h1 class="forum-post-title">
                            {{ $post->title }}
                        </h1>


                        <div class="forum-post-header-meta">

                            <div class="forum-post-author">

                                <div class="forum-post-author-avatar">

                                    @if($post->user->avatar)
                                        <img
                                            src="{{ asset($post->user->avatar) }}"
                                            alt="{{ $post->user->game_name ?? $post->user->name }}"
                                        >
                                    @else
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
                                    @endif

                                </div>

                                <div class="forum-post-author-info">

                                    <strong>
                                        {{ $post->user->game_name ?? $post->user->name }}
                                    </strong>

                                    <span>
                                        Đăng {{ $post->created_at->diffForHumans() }}
                                    </span>

                                </div>

                            </div>


                            <div class="forum-post-stats">

                                <div>
                                    <strong>{{ $post->views_count }}</strong>
                                    <span>lượt xem</span>
                                </div>

                                <div>
                                    <strong>{{ $post->comments_count }}</strong>
                                    <span>trả lời</span>
                                </div>

                                <div>
                                    <strong>{{ $post->likes_count }}</strong>
                                    <span>thích</span>
                                </div>

                            </div>

                        </div>

                    </header>


                    {{-- POST CONTENT --}}
                    <div class="forum-post-body">

                        {!! nl2br(e($post->content)) !!}

                    </div>


                    {{-- POST FOOTER --}}
                    <footer class="forum-post-footer">

                        <div class="forum-post-footer-left">

                            <span>
                                {{ $post->created_at->format('d/m/Y H:i') }}
                            </span>

                            @if($post->updated_at && $post->updated_at->gt($post->created_at))
                                <span>
                                    · Đã chỉnh sửa
                                </span>
                            @endif

                        </div>


                        <div class="forum-post-actions">

                            @auth

                                @if($post->user_id === auth()->id())

                                    <a
                                        href="{{ route('forum.edit', $post->id) }}"
                                        class="forum-post-action"
                                    >
                                        ✎
                                        Sửa
                                    </a>

                                    <form
                                        action="{{ route('forum.destroy', $post->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="forum-post-action danger"
                                        >
                                            ✕
                                            Xóa
                                        </button>
                                    </form>

                                @endif

                            @endauth

                        </div>

                    </footer>

                </article>


                {{-- =====================================================
                    COMMENTS
                ====================================================== --}}
                <section class="forum-discussion">

                    <div class="forum-discussion-header">

                        <div>
                            <span class="forum-discussion-eyebrow">
                                DISCUSSION
                            </span>

                            <h2>
                                Thảo luận
                                <span>{{ $post->comments_count }}</span>
                            </h2>
                        </div>

                    </div>


                    {{-- Comment form --}}
                    @auth

                        @if(!$post->is_locked)

                            <form
                                method="POST"
                                action="{{ route('forum.comment.store', $post) }}"
                                class="forum-comment-form-card"
                            >
                                @csrf

                                <div class="forum-comment-form-avatar">

                                    @if(auth()->user()->avatar)

                                        <img
                                            src="{{ asset(auth()->user()->avatar) }}"
                                            alt="{{ auth()->user()->game_name ?? auth()->user()->name }}"
                                        >

                                    @else

                                        {{
                                            strtoupper(
                                                substr(
                                                    auth()->user()->game_name
                                                    ?? auth()->user()->name
                                                    ?? 'U',
                                                    0,
                                                    1
                                                )
                                            )
                                        }}

                                    @endif

                                </div>


                                <div class="forum-comment-form-content">

                                    <textarea
                                        id="forumCommentInput"
                                        name="content"
                                        class="forum-comment-input @if(!old('parent_id')) @error('content') is-invalid @enderror @endif"
                                        placeholder="Viết bình luận của bạn..."
                                        rows="3"
                                        required
                                    >{{ !old('parent_id') ? old('content') : '' }}</textarea>

                                    @if(!old('parent_id'))
                                        @error('content')
                                            <small class="forum-form-error">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    @endif


                                    <div class="forum-comment-form-footer">

                                        <span>
                                            Hãy giữ cuộc thảo luận văn minh và tôn trọng.
                                        </span>

                                        <button
                                            type="submit"
                                            class="forum-comment-submit"
                                        >
                                            Gửi bình luận
                                        </button>

                                    </div>

                                </div>

                            </form>

                        @else

                            <div class="forum-locked-notice">

                                <span>🔒</span>

                                <div>
                                    <strong>Chủ đề đã được khóa</strong>

                                    <p>
                                        Chủ đề này hiện không nhận thêm bình luận.
                                    </p>
                                </div>

                            </div>

                        @endif

                    @else

                        <div class="forum-login-comment">

                            <div>

                                <strong>
                                    Tham gia cuộc thảo luận
                                </strong>

                                <p>
                                    Đăng nhập để bình luận và chia sẻ ý kiến của bạn.
                                </p>

                            </div>

                            <a href="{{ route('login') }}">
                                Đăng nhập
                            </a>

                        </div>

                    @endauth


                    {{-- Comments --}}
                    <div class="forum-comments">

                        @forelse($post->comments as $comment)

                            <article class="forum-comment" id="comment-{{ $comment->id }}">

                                <div class="forum-comment-avatar">

                                    @if($comment->user->avatar)
                                        <img
                                            src="{{ asset($comment->user->avatar) }}"
                                            alt="{{ $comment->user->game_name ?? $comment->user->name }}"
                                        >
                                    @else
                                        {{
                                            strtoupper(
                                                substr(
                                                    $comment->user->game_name
                                                    ?? $comment->user->name
                                                    ?? 'U',
                                                    0,
                                                    1
                                                )
                                            )
                                        }}
                                    @endif

                                </div>


                                <div class="forum-comment-content">

                                    <div class="forum-comment-header">

                                        <div>
                                            <strong>
                                                {{ $comment->user->game_name ?? $comment->user->name }}
                                            </strong>

                                            <span>
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        @if($comment->likes_count > 0)
                                            <span class="forum-comment-like-count">
                                                ♥ {{ $comment->likes_count }}
                                            </span>
                                        @endif

                                    </div>


                                    <div class="forum-comment-text">
                                        {!! preg_replace('/(@[^\s<]+)/u', '<span class="forum-mention">$1</span>', nl2br(e($comment->content))) !!}
                                    </div>


                                    <div class="forum-comment-actions">

                                        <button
                                            type="button"
                                            class="forum-comment-action"
                                            disabled
                                        >
                                            ♥ Thích
                                        </button>

                                        @if($post->is_locked)
                                            <button
                                                type="button"
                                                class="forum-comment-action"
                                                disabled
                                            >
                                                Trả lời
                                            </button>
                                        @else
                                            @auth
                                                <button
                                                    type="button"
                                                    class="forum-comment-action btn-reply"
                                                    data-comment-id="{{ $comment->id }}"
                                                    data-author="{{ $comment->user->game_name ?? $comment->user->name }}"
                                                >
                                                    💬 Trả lời
                                                </button>
                                            @else
                                                <a
                                                    href="{{ route('login') }}"
                                                    class="forum-comment-action"
                                                >
                                                    💬 Trả lời
                                                </a>
                                            @endauth
                                        @endif

                                    </div>


                                    {{-- Replies --}}
                                    @if($comment->replies && $comment->replies->count())

                                        <div class="forum-comment-replies">

                                            @foreach($comment->replies as $reply)

                                                <article class="forum-comment reply" id="comment-{{ $reply->id }}">

                                                    <div class="forum-comment-avatar small">

                                                        @if($reply->user->avatar)
                                                            <img
                                                                src="{{ asset($reply->user->avatar) }}"
                                                                alt="{{ $reply->user->game_name ?? $reply->user->name }}"
                                                            >
                                                        @else
                                                            {{
                                                                strtoupper(
                                                                    substr(
                                                                        $reply->user->game_name
                                                                        ?? $reply->user->name
                                                                        ?? 'U',
                                                                        0,
                                                                        1
                                                                    )
                                                                )
                                                            }}
                                                        @endif

                                                    </div>


                                                    <div class="forum-comment-content">

                                                        <div class="forum-comment-header">

                                                            <div>
                                                                <strong>
                                                                    {{ $reply->user->game_name ?? $reply->user->name }}
                                                                </strong>

                                                                <span>
                                                                    {{ $reply->created_at->diffForHumans() }}
                                                                </span>
                                                            </div>

                                                        </div>

                                                        <div class="forum-comment-text">
                                                            {!! preg_replace('/(@[^\s<]+)/u', '<span class="forum-mention">$1</span>', nl2br(e($reply->content))) !!}
                                                        </div>

                                                        <div class="forum-comment-actions">

                                                            <button
                                                                type="button"
                                                                class="forum-comment-action"
                                                                disabled
                                                            >
                                                                ♥ Thích
                                                            </button>

                                                            @if($post->is_locked)
                                                                <button
                                                                    type="button"
                                                                    class="forum-comment-action"
                                                                    disabled
                                                                >
                                                                    Trả lời
                                                                </button>
                                                            @else
                                                                @auth
                                                                    <button
                                                                        type="button"
                                                                        class="forum-comment-action btn-reply"
                                                                        data-comment-id="{{ $comment->id }}"
                                                                        data-author="{{ $reply->user->game_name ?? $reply->user->name }}"
                                                                    >
                                                                        💬 Trả lời
                                                                    </button>
                                                                @else
                                                                    <a
                                                                        href="{{ route('login') }}"
                                                                        class="forum-comment-action"
                                                                    >
                                                                        💬 Trả lời
                                                                    </a>
                                                                @endauth
                                                            @endif

                                                        </div>

                                                    </div>

                                                </article>

                                            @endforeach

                                        </div>

                                    @endif

                                    {{-- Reply Form --}}
                                    @auth
                                        @if(!$post->is_locked)
                                            <div
                                                class="forum-reply-form-wrapper"
                                                id="reply-box-{{ $comment->id }}"
                                                style="{{ old('parent_id') == $comment->id ? 'display: block;' : 'display: none;' }}"
                                            >
                                                <form
                                                    method="POST"
                                                    action="{{ route('forum.comment.store', $post) }}"
                                                    class="forum-reply-form"
                                                >
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                                    <div class="forum-reply-target-bar">
                                                        <span>
                                                            Đang trả lời <strong class="reply-target-name">{{ $comment->user->game_name ?? $comment->user->name }}</strong>
                                                        </span>
                                                        <button
                                                            type="button"
                                                            class="forum-reply-close-btn btn-cancel-reply"
                                                            data-comment-id="{{ $comment->id }}"
                                                            title="Hủy"
                                                        >
                                                            ✕ Hủy
                                                        </button>
                                                    </div>

                                                    <div class="forum-reply-inner">
                                                        <div class="forum-comment-form-avatar small">
                                                            @if(auth()->user()->avatar)
                                                                <img
                                                                    src="{{ asset(auth()->user()->avatar) }}"
                                                                    alt="{{ auth()->user()->game_name ?? auth()->user()->name }}"
                                                                >
                                                            @else
                                                                {{
                                                                    strtoupper(
                                                                        substr(
                                                                            auth()->user()->game_name
                                                                            ?? auth()->user()->name
                                                                            ?? 'U',
                                                                            0,
                                                                            1
                                                                        )
                                                                    )
                                                                }}
                                                            @endif
                                                        </div>

                                                        <div class="forum-reply-content">
                                                            <textarea
                                                                name="content"
                                                                class="forum-comment-input forum-reply-input @if(old('parent_id') == $comment->id) @error('content') is-invalid @enderror @endif"
                                                                placeholder="Viết câu trả lời của bạn..."
                                                                rows="2"
                                                                required
                                                            >{{ old('parent_id') == $comment->id ? old('content') : '' }}</textarea>

                                                            @if(old('parent_id') == $comment->id)
                                                                @error('content')
                                                                    <small class="forum-form-error">
                                                                        {{ $message }}
                                                                    </small>
                                                                @enderror
                                                            @endif

                                                            <div class="forum-comment-form-footer">
                                                                <span>
                                                                    Hãy giữ cuộc thảo luận văn minh và tôn trọng.
                                                                </span>

                                                                <div class="forum-reply-btn-actions">
                                                                    <button
                                                                        type="button"
                                                                        class="forum-reply-cancel-btn btn-cancel-reply"
                                                                        data-comment-id="{{ $comment->id }}"
                                                                    >
                                                                        Hủy
                                                                    </button>

                                                                    <button
                                                                        type="submit"
                                                                        class="forum-comment-submit"
                                                                    >
                                                                        Gửi trả lời
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth

                                </div>

                            </article>

                        @empty

                            <div class="forum-comments-empty">

                                <div class="forum-comments-empty-icon">
                                    💬
                                </div>

                                <h3>
                                    Chưa có bình luận
                                </h3>

                                <p>
                                    Hãy là người đầu tiên chia sẻ suy nghĩ về chủ đề này.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </section>

            </main>


            {{-- =====================================================
                SIDEBAR
            ====================================================== --}}
            <aside class="forum-show-sidebar">

                {{-- Category --}}
                <div class="forum-show-side-card">

                    <div class="forum-show-side-label">
                        DANH MỤC
                    </div>

                    <a
                        href="{{ route('forum.category', $post->category->slug) }}"
                        class="forum-show-side-category"
                    >

                        <span class="forum-show-side-category-icon">
                            {{ $post->category->icon ?: '💬' }}
                        </span>

                        <span>
                            {{ $post->category->name }}
                        </span>

                        <span>
                            ›
                        </span>

                    </a>

                </div>


                {{-- Author --}}
                <div class="forum-show-side-card">

                    <div class="forum-show-side-label">
                        TÁC GIẢ
                    </div>

                    <div class="forum-show-author">

                        <div class="forum-show-author-avatar">

                            @if($post->user->avatar)
                                <img
                                    src="{{ asset($post->user->avatar) }}"
                                    alt="{{ $post->user->game_name ?? $post->user->name }}"
                                >
                            @else
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
                            @endif

                        </div>

                        <div>
                            <strong>
                                {{ $post->user->game_name ?? $post->user->name }}
                            </strong>

                            <span>
                                Thành viên Mini Cine
                            </span>
                        </div>

                    </div>

                </div>


                {{-- Topic stats --}}
                <div class="forum-show-side-card">

                    <div class="forum-show-side-label">
                        THỐNG KÊ CHỦ ĐỀ
                    </div>

                    <div class="forum-show-stat-list">

                        <div>
                            <span>Lượt xem</span>
                            <strong>{{ $post->views_count }}</strong>
                        </div>

                        <div>
                            <span>Trả lời</span>
                            <strong>{{ $post->comments_count }}</strong>
                        </div>

                        <div>
                            <span>Lượt thích</span>
                            <strong>{{ $post->likes_count }}</strong>
                        </div>

                    </div>

                </div>


                {{-- Community --}}
                <div class="forum-show-side-card forum-show-community-card">

                    <span class="forum-form-side-label">
                        MINI CINE COMMUNITY
                    </span>

                    <h3>
                        Thích chủ đề này?
                    </h3>

                    <p>
                        Khám phá thêm những cuộc thảo luận khác
                        trong cộng đồng Mini Cine.
                    </p>

                    <a href="{{ route('forum.index') }}">
                        Xem Forum
                        <span>→</span>
                    </a>

                </div>

            </aside>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const replyButtons = document.querySelectorAll('.btn-reply');
    const cancelButtons = document.querySelectorAll('.btn-cancel-reply');

    replyButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const commentId = this.dataset.commentId;
            const author = this.dataset.author;
            const replyBox = document.getElementById('reply-box-' + commentId);

            if (!replyBox) return;

            // Ẩn tất cả các khung trả lời khác để gọn gàng
            document.querySelectorAll('.forum-reply-form-wrapper').forEach(function (box) {
                if (box !== replyBox) {
                    box.style.display = 'none';
                }
            });

            // Hiển thị khung trả lời
            replyBox.style.display = 'block';

            // Cập nhật tên người nhận phản hồi
            const targetNameEl = replyBox.querySelector('.reply-target-name');
            if (targetNameEl && author) {
                targetNameEl.textContent = author;
            }

            // Focus và chèn @tên
            const textarea = replyBox.querySelector('textarea[name="content"]');
            if (textarea) {
                if (author) {
                    const mention = '@' + author.trim() + ' ';
                    if (!textarea.value.trim()) {
                        textarea.value = mention;
                    } else if (!textarea.value.includes(mention)) {
                        textarea.value = mention + textarea.value;
                    }
                }
                textarea.focus();
                const len = textarea.value.length;
                textarea.setSelectionRange(len, len);
            }

            replyBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    cancelButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const commentId = this.dataset.commentId;
            const replyBox = document.getElementById('reply-box-' + commentId);
            if (replyBox) {
                replyBox.style.display = 'none';
                const textarea = replyBox.querySelector('textarea[name="content"]');
                if (textarea) {
                    textarea.value = '';
                }
            }
        });
    });
});
</script>

@endsection