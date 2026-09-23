<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function coupons()
    {
        return view('admin.pages.premium.coupons');
    }
    public function subscriptions()
    {
        return view('admin.pages.premium.subscriptions');
    }
    public function transactions()
    {
        return view('admin.pages.premium.transactions');
    }
    public function plans()
    {
        return view('admin.pages.premium.plans');
    }
}
