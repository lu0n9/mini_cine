<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function email()
    {
        return view('admin.pages.notifications.email');
    }
    public function notification()
    {
        return view('admin.pages.notifications.notifications');
    }
}
