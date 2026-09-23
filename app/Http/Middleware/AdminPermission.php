<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        if (!$admin->hasPermission($permission)) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}