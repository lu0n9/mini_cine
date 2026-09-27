<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckUserBan;
use App\Http\Middleware\ApplySystemSettings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.ban' => CheckUserBan::class,
            'admin.permission' => \App\Http\Middleware\AdminPermission::class,
            'admin.activity-log' => \App\Http\Middleware\RecordAdminActivity::class,
        ]);
        $middleware->web(append: [ApplySystemSettings::class]);
        // 1. Khi CHƯA đăng nhập mà vào route bảo vệ -> chuyển về trang login tương ứng
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            return route('login');
        });

        // 2. Khi ĐÃ đăng nhập mà vào lại trang login -> chuyển về dashboard tương ứng
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.dashboard');
            }
            return route('home'); // hoặc route client của bạn
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
