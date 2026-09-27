@extends('client.layouts.master')
@section('title', 'Tin tức — MINI CINE')
@section('description', 'Tin tức điện ảnh, thông tin phim mới và cập nhật từ MINI CINE.')

@push('styles')
<style>
    .news-page{max-width:1180px;margin:0 auto}.news-heading{margin:12px 0 28px}.news-heading h1{margin:0 0 8px;font-size:clamp(30px,5vw,44px)}.news-heading p{margin:0;color:#a9adbb}
    .news-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}.news-card{overflow:hidden;border:1px solid rgba(255,255,255,.1);border-radius:15px;background:#12141c;color:#f8fafc;text-decoration:none;transition:transform .2s,border-color .2s}.news-card:hover{transform:translateY(-3px);border-color:#7444d8}.news-cover{aspect-ratio:16/9;background:#20232d}.news-cover img{width:100%;height:100%;object-fit:cover}.news-card-body{padding:16px}.news-card h2{margin:0 0 8px;font-size:18px;line-height:1.45}.news-excerpt{display:-webkit-box;overflow:hidden;margin:0 0 12px;color:#b5b8c4;line-height:1.6;-webkit-line-clamp:3;-webkit-box-orient:vertical}.news-date{color:#9296a4;font-size:12px}.news-pagination{margin-top:28px}.news-empty{padding:55px 20px;text-align:center;border:1px solid rgba(255,255,255,.1);border-radius:15px;color:#b5b8c4}
    @media(max-width:850px){.news-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.news-grid{grid-template-columns:1fr;gap:14px}}
</style>
@endpush

@section('content')
<section class="news-page">
    <header class="news-heading"><h1>Tin tức</h1><p>Tin điện ảnh và những cập nhật mới nhất từ MINI CINE.</p></header>
    @if($news->isNotEmpty())
        <div class="news-grid">
            @foreach($news as $article)
                <a class="news-card" href="{{ route('news.show', $article->slug) }}">
                    <div class="news-cover">
                        <img src="{{ $article->cover_image ? asset('storage/' . $article->cover_image) : asset('images/poster-default.png') }}" alt="{{ $article->title }}" loading="lazy">
                    </div>
                    <div class="news-card-body">
                        <h2>{{ $article->title }}</h2>
                        @if($article->excerpt)<p class="news-excerpt">{{ $article->excerpt }}</p>@endif
                        <div class="news-date">{{ ($article->published_at ?? $article->created_at)?->format('d/m/Y') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($news->hasPages())<div class="news-pagination">{{ $news->links('pagination::custom') }}</div>@endif
    @else
        <div class="news-empty">Chưa có tin tức được xuất bản.</div>
    @endif
</section>
@endsection
