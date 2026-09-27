<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RecordAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();
        $response = $next($request);

        if (!$admin || !$this->shouldRecord($request, $response)) {
            return $response;
        }

        try {
            $route = $request->route();
            $routeName = $route?->getName() ?: $request->path();
            $parameters = collect($route?->parameters() ?? [])
                ->map(function ($value, $key) {
                    if (str_contains(strtolower((string) $key), 'token')) {
                        return null;
                    }
                    if (is_object($value) && method_exists($value, 'getRouteKey')) {
                        $value = $value->getRouteKey();
                    } elseif (is_array($value) || is_object($value)) {
                        return null;
                    }
                    return is_scalar($value) ? $key . ':' . $value : null;
                })
                ->filter()
                ->take(4)
                ->implode(', ');

            $action = $this->actionLabel($request, (string) $routeName);
            $subject = $routeName . ($parameters !== '' ? ' · ' . $parameters : '');

            AdminActivityLog::create([
                'admin_id' => $admin->getKey(),
                'admin_name' => $admin->name ?: $admin->email,
                'admin_email' => $admin->email,
                'action' => $action,
                'route_name' => $routeName,
                'subject' => mb_substr($subject, 0, 255),
                'ip_address' => $request->ip(),
                'status_code' => $response->getStatusCode(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            // Do not turn a successful admin action into a failed request if audit storage is unavailable.
            Log::warning('Could not write admin activity log.', ['error' => $exception->getMessage()]);
        }

        return $response;
    }

    private function shouldRecord(Request $request, Response $response): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && $response->getStatusCode() >= 200
            && $response->getStatusCode() < 400
            && !str_starts_with((string) $request->route()?->getName(), 'admin.system.activity-log');
    }

    private function actionLabel(Request $request, string $routeName): string
    {
        if (str_contains($routeName, 'restore')) return 'Khôi phục';
        if (str_contains($routeName, 'clear')) return 'Xóa cache';
        if (str_contains($routeName, 'toggle')) return 'Đổi trạng thái';

        return match ($request->method()) {
            'POST' => 'Tạo / thực hiện',
            'PUT', 'PATCH' => 'Cập nhật',
            'DELETE' => 'Xóa',
            default => $request->method(),
        };
    }
}
