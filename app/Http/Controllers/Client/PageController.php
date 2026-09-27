<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('client.pages.pages.show', compact('page'));
    }
}