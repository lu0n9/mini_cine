<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBan
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->banHasExpired()) {
            $user->activateFromBan();

            return $next($request);
        }

        if ($user->isBanned()) {
            Auth::guard('web')->logout();

            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ]);
        }

        return $next($request);
    }
}