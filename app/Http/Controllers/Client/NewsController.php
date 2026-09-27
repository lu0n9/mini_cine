<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()->latest('published_at')->latest('created_at')->paginate(12);

        return view('client.pages.news.index', compact('news'));
    }

    public function show(string $slug)
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();
        $relatedNews = News::published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('client.pages.news.show', compact('article', 'relatedNews'));
    }
}
