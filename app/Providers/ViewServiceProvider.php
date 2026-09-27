<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Movie;
use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::share('totalMovie',Movie::count());
        View::share('userCount', User::count());
        View::share('adminCount', Admin::count());
        View::share(
            'newCommentCount',
            Comment::whereIn('status', ['pending', 'ai_review'])->count());
        View::share(
            'newReportCount',
            Report::where('status', 'pending')->count());

        View::composer('client.partials.header', function ($view) {
            $user = auth('web')->user();

            $view->with([
                'headerNotifications' => $user
                    ? $user->notifications()->latest()->take(5)->get()
                    : collect(),
                'unreadNotificationCount' => $user
                    ? $user->notifications()->whereNull('read_at')->count()
                    : 0,
            ]);
        });
        /*
        |--------------------------------------------------------------------------
        | Main Menu
        |--------------------------------------------------------------------------
        */
        View::composer('client.layouts.master', function ($view) {

            $mainMenus = Menu::query()
                ->with([
                    'children' => function ($query) {
                        $query
                            ->where('is_active', true)
                            ->orderBy('sort_order');
                    }
                ])
                ->where('location', 'main')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();


            /*
            |--------------------------------------------------------------------------
            | Footer Menu
            |--------------------------------------------------------------------------
            */
            $footerMenus = Menu::query()
                ->with([
                    'children' => function ($query) {
                        $query
                            ->where('is_active', true)
                            ->orderBy('sort_order');
                    }
                ])
                ->where('location', 'footer')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();


            $view->with([
                'mainMenus' => $mainMenus,
                'footerMenus' => $footerMenus,
            ]);
        });

    }
}
