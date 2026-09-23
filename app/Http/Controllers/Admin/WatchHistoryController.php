<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\WatchHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WatchHistoryController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Thống kê tổng quan
        |--------------------------------------------------------------------------
        */

        $totalHistories = WatchHistory::count();

        // Phim / tập đang xem dở
        $unfinishedCount = WatchHistory::query()
            ->where('duration', '>', 0)
            ->whereColumn('watch_time', '<', 'duration')
            ->count();

        // Phim / tập đã xem xong
        $completedCount = WatchHistory::query()
            ->where('duration', '>', 0)
            ->whereColumn('watch_time', '>=', 'duration')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Tiến độ trung bình
        |--------------------------------------------------------------------------
        */

        $averageProgress = 0;

        if ($totalHistories > 0) {
            $historiesForProgress = WatchHistory::query()
                ->where('duration', '>', 0)
                ->get([
                    'watch_time',
                    'duration',
                ]);

            if ($historiesForProgress->count() > 0) {
                $totalProgress = $historiesForProgress->sum(function ($history) {
                    return min(
                        ($history->watch_time / $history->duration) * 100,
                        100
                    );
                });

                $averageProgress = round(
                    $totalProgress / $historiesForProgress->count()
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Completion rate
        |--------------------------------------------------------------------------
        */

        $completionRate = 0;

        if ($totalHistories > 0) {
            $completionRate = round(
                ($completedCount / $totalHistories) * 100
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Danh sách lịch sử xem
        |--------------------------------------------------------------------------
        */

        $watchHistories = WatchHistory::query()
            ->with([
                'user:id,name,email',
                'movie:id,title,slug,poster',
                'episode:id,movie_id,season_id,episode_number,name,duration',
                'episode.season:id,movie_id,season_number',
            ])
            ->latest('last_watched_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.pages.statistical.watch_history',
            compact(
                'watchHistories',
                'unfinishedCount',
                'completedCount',
                'averageProgress',
                'completionRate'
            )
        );
    }
}