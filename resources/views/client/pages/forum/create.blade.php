@extends('client.layouts.master')

@section('title', 'Viết bài - Forum Mini Cine')

@section('content')

<div class="forum-page forum-form-page">

    <div class="forum-container">

        {{-- PAGE HEADER --}}
        <header class="forum-form-hero">

            <div class="forum-form-hero-content">

                <a
                    href="{{ route('forum.index') }}"
                    class="forum-back-link"
                >
                    <span>‹</span>
                    Forum Mini Cine
                </a>

                <h1>
                    Tạo chủ đề mới
                </h1>

                <p>
                    Chia sẻ suy nghĩ, cảm nhận hoặc bắt đầu một cuộc thảo luận
                    cùng cộng đồng Mini Cine.
                </p>

            </div>

        </header>


        {{-- FORM --}}
        <form
            action="{{ route('forum.store') }}"
            method="POST"
            class="forum-form"
        >
            @csrf

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