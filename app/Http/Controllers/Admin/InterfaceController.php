<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    public function banners()
    {
        return view('admin.pages.interface.banners');
    }
    public function homepage()
    {
        return view('admin.pages.interface.homepage');
    }
    public function pages()
    {
        return view('admin.pages.interface.pages');
    }
}
