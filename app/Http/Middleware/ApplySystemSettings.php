<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplySystemSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $settings = SystemSetting::getSettings();
        } catch (\Throwable $exception) {
            Log::warning('Could not load site settings.', ['error' => $exception->getMessage()]);
            $settings = new SystemSetting([
                'site_name' => 'MINI CINE',
                'contact_email' => null,
                'contact_hotline' => null,
                'timezone' => config('app.timezone', 'Asia/Ho_Chi_Minh'),
                'maintenance_mode' => false,
                'player_autoplay' => true,
                'player_auto_next' => true,
                'player_pip' => true,
                'allow_registration' => true,
            ]);
        }

        $timezone = in_array($settings->timezone, timezone_identifiers_list(), true)
            ? $settings->timezone
            : config('app.timezone', 'Asia/Ho_Chi_Minh');
        config([
            'app.name' => $settings->site_name ?: config('app.name'),
            'app.timezone' => $timezone,
        ]);
        date_default_timezone_set($timezone);
        View::share('systemSettings', $settings);

        if ($settings->maintenance_mode && !$request->is('admin', 'admin/*')) {
            return response()->view('errors.maintenance', compact('settings'), 503);
        }

        return $next($request);
    }
}
