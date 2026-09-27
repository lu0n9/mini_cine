@extends('admin.layouts.master')
@section('content')
<section class="page">
    <div class="page-head"><div><h3>Chỉnh sửa tin tức</h3><p>Cập nhật nội dung, ảnh bìa hoặc trạng thái xuất bản.</p></div></div>
    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.pages.news._form', ['article' => $news, 'submitLabel' => 'Lưu thay đổi'])
    </form>
</section>
@endsection
