@extends('admin.layouts.master')
@section('content')
<section class="page">
    <div class="page-head"><div><h3>Viết tin tức</h3><p>Tạo bài viết mới cho trang tin.</p></div></div>
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.pages.news._form', ['article' => null, 'submitLabel' => 'Tạo bài viết'])
    </form>
</section>
@endsection
