<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function seo()
    {
        return view('admin.pages.seo.seo');
    }
    public function siteMap()
    {
        return view('admin.pages.seo.sitemap');
    }
}
