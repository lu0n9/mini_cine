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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
