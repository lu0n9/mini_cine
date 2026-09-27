@extends('client.layouts.master')

@section('title', 'Chỉnh sửa bài viết - Forum Mini Cine')

@section('content')

<div class="forum-page forum-form-page">

    <div class="forum-container">

        {{-- PAGE HEADER --}}
        <header class="forum-form-hero">

            <div class="forum-form-hero-content">

                <a
                    href="{{ route('forum.show', $post->slug) }}"
                    class="forum-back-link"
                >
                    <span>‹</span>
                    Quay lại bài viết
                </a>

                <h1>
                    Chỉnh sửa bài viết
                </h1>

                <p>
                    Cập nhật nội dung bài viết của bạn rồi lưu lại thay đổi.
                </p>

            </div>

            <div class="forum-edit-status">
                <span class="forum-status-dot"></span>
                Đang chỉnh sửa
            </div>

        </header>


        {{-- FORM --}}
        <form
            action="{{ route('forum.update', $post->id) }}"
            method="POST"
            class="forum-form"
        >
            @csrf
            @method('PUT')

            @include('client.pages.forum._form')

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const title = document.getElementById('title');
    const titleCount = document.getElementById('titleCount');

    const content = document.getElementById('content');
    const contentCount = document.getElementById('contentCount');

    function updateTitleCount() {
        if (!title || !titleCount) return;

        titleCount.textContent = title.value.length;
    }

    function updateContentCount() {
        if (!content || !contentCount) return;

        contentCount.textContent = content.value.length.toLocaleString('vi-VN');
    }

    if (title) {
        title.addEventListener('input', updateTitleCount);
        updateTitleCount();
    }

    if (content) {
        content.addEventListener('input', updateContentCount);
        updateContentCount();
    }

});
</script>
@endpush