<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function activityLog()
    {
        return view('admin.pages.system.activity_logs');
    }
    public function api()
    {
        return view('admin.pages.system.api');
    }
    public function cache()
    {
        return view('admin.pages.system.cache');
    }
    public function backup()
    {
        return view('admin.pages.system.backup');
    }
    public function cron()
    {
        return view('admin.pages.system.cron');
    }
    public function storage()
    {
        return view('admin.pages.system.storage');
    }
}
