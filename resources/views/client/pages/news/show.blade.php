@extends('client.layouts.master')
@section('title', $article->title . ' — MINI CINE')
@section('description', $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 160))

@push('styles')
<style>
    .news-article-page{max-width:900px;margin:0 auto}.news-back{display:inline-block;margin:4px 0 20px;color:#c4b5fd;text-decoration:none}.news-article-head h1{margin:0 0 12px;font-size:clamp(30px,5vw,48px);line-height:1.2}.news-article-meta{margin-bottom:24px;color:#a9adbb}.news-article-cover{width:100%;max-height:520px;object-fit:cover;border-radius:18px;margin-bottom:28px}.news-article-excerpt{font-size:20px;line-height:1.7;color:#d1d5df}.news-article-content{font-size:17px;line-height:1.9;color:#e3e5eb;overflow-wrap:anywhere}.news-article-content p{margin:0 0 1.2em}.news-related{margin-top:52px;padding-top:24px;border-top:1px solid rgba(255,255,255,.12)}.news-related h2{margin:0 0 16px}.news-related-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.news-related a{color:#f8fafc;text-decoration:none}.news-related img{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:10px;margin-bottom:8px}.news-related strong{line-height:1.5}@media(max-width:650px){.news-related-list{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<article class="news-article-page">
    <a class="news-back" href="{{ route('news.index') }}">← Tất cả tin tức</a>
    <header class="news-article-head">
        <h1>{{ $article->title }}</h1>
        <div class="news-article-meta">{{ ($article->published_at ?? $article->created_at)?->format('d/m/Y H:i') }}</div>
        @if($article->excerpt)<p class="news-article-excerpt">{{ $article->excerpt }}</p>@endif
    </header>
    @if($article->cover_image)
        <img class="news-article-cover" src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }}">
    @endif
    <div class="news-article-content">{!! nl2br(e($article->content)) !!}</div>

    @if($relatedNews->isNotEmpty())
        <aside class="news-related">
            <h2>Tin mới khác</h2>
            <div class="news-related-list">
                @foreach($relatedNews as $related)
                    <a href="{{ route('news.show', $related->slug) }}">
                        <img src="{{ $related->cover_image ? asset('storage/' . $related->cover_image) : asset('images/poster-default.png') }}" alt="" loading="lazy">
                        <strong>{{ $related->title }}</strong>
                    </a>
                @endforeach
            </div>
        </aside>
    @endif
</article>
@endsection
