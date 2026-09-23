<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MovieView;
use Illuminate\Support\Collection;

class TrafficController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | LẤY DỮ LIỆU VIEW
        |--------------------------------------------------------------------------
        */

        $views = MovieView::query()
            ->whereNotNull('user_agent')
            ->get([
                'user_agent',
                'ip_address',
                'created_at',
            ]);


        /*
        |--------------------------------------------------------------------------
        | THIẾT BỊ
        |--------------------------------------------------------------------------
        */

        $deviceCounts = [
            'Mobile' => 0,
            'Desktop' => 0,
            'Smart TV' => 0,
        ];

        foreach ($views as $view) {

            $device = $this->detectDevice(
                $view->user_agent
            );

            $deviceCounts[$device]++;
        }

        $totalDeviceViews = array_sum($deviceCounts);

        $devices = collect($deviceCounts)
            ->map(function ($count, $name) use ($totalDeviceViews) {

                return [
                    'name' => $name,
                    'count' => $count,
                    'percentage' => $totalDeviceViews > 0
                        ? round(($count / $totalDeviceViews) * 100)
                        : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | TRÌNH DUYỆT
        |--------------------------------------------------------------------------
        */

        $browserCounts = [
            'Chrome' => 0,
            'Safari' => 0,
            'Firefox' => 0,
            'Khác' => 0,
        ];

        foreach ($views as $view) {

            $browser = $this->detectBrowser(
                $view->user_agent
            );

            $browserCounts[$browser]++;
        }

        $totalBrowserViews = array_sum($browserCounts);

        $browsers = collect($browserCounts)
            ->map(function ($count, $name) use ($totalBrowserViews) {

                return [
                    'name' => $name,
                    'count' => $count,
                    'percentage' => $totalBrowserViews > 0
                        ? round(($count / $totalBrowserViews) * 100)
                        : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | QUỐC GIA
        |--------------------------------------------------------------------------
        |
        | movie_views hiện tại chưa có country.
        |
        */

        $countries = collect([
            [
                'name' => 'Không xác định',
                'count' => MovieView::count(),
                'percentage' => 100,
            ],
        ]);


        return view(
            'admin.pages.statistical.traffic',
            compact(
                'devices',
                'browsers',
                'countries'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DETECT DEVICE
    |--------------------------------------------------------------------------
    */

    private function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Desktop';
        }

        $ua = strtolower($userAgent);


        /*
        | Smart TV
        */

        if (
            str_contains($ua, 'smart-tv') ||
            str_contains($ua, 'smarttv') ||
            str_contains($ua, 'webos') ||
            str_contains($ua, 'tizen') ||
            str_contains($ua, 'hbbtv') ||
            str_contains($ua, 'netcast') ||
            str_contains($ua, 'roku')
        ) {
            return 'Smart TV';
        }


        /*
        | Mobile
        */

        if (
            str_contains($ua, 'mobile') ||
            str_contains($ua, 'android') ||
            str_contains($ua, 'iphone') ||
            str_contains($ua, 'ipad') ||
            str_contains($ua, 'ipod')
        ) {
            return 'Mobile';
        }


        /*
        | Mặc định
        */

        return 'Desktop';
    }


    /*
    |--------------------------------------------------------------------------
    | DETECT BROWSER
    |--------------------------------------------------------------------------
    */

    private function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Khác';
        }

        $ua = strtolower($userAgent);


        /*
        | Edge phải kiểm tra trước Chrome
        */

        if (
            str_contains($ua, 'edg/') ||
            str_contains($ua, 'edge/')
        ) {
            return 'Chrome';
        }


        /*
        | Firefox
        */

        if (str_contains($ua, 'firefox')) {
            return 'Firefox';
        }


        /*
        | Chrome
        */

        if (
            str_contains($ua, 'chrome') ||
            str_contains($ua, 'crios')
        ) {
            return 'Chrome';
        }


        /*
        | Safari
        |
        | Safari thường có "safari" nhưng không có chrome.
        */

        if (
            str_contains($ua, 'safari')
        ) {
            return 'Safari';
        }


        return 'Khác';
    }
}