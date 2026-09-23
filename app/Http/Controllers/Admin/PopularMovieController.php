<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\MovieView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PopularMovieController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TOP LƯỢT XEM 30 NGÀY
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $topViewedMovies = MovieView::query()
            ->select(
                'movie_id',
                DB::raw('COUNT(*) as views_count')
            )
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('movie_id')
            ->with([
                'movie:id,title,slug'
            ])
            ->groupBy('movie_id')
            ->orderByDesc('views_count')
            ->limit(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TÍNH PHẦN TRĂM CHO BAR CHART
        |--------------------------------------------------------------------------
        */

        $maxViews = $topViewedMovies->max('views_count') ?? 0;

        $topViewedMovies->transform(function ($item) use ($maxViews) {

            $item->bar_height = $maxViews > 0
                ? round(($item->views_count / $maxViews) * 100)
                : 0;

            return $item;
        });


        /*
        |--------------------------------------------------------------------------
        | TOP YÊU THÍCH
        |--------------------------------------------------------------------------
        */

        $topFavoriteMovies = Favorite::query()
            ->select(
                'movie_id',
                DB::raw('COUNT(*) as favorites_count')
            )
            ->whereNotNull('movie_id')
            ->with([
                'movie:id,title,slug'
            ])
            ->groupBy('movie_id')
            ->orderByDesc('favorites_count')
            ->limit(4)
            ->get();


        return view(
            'admin.pages.statistical.popular_stats',
            compact(
                'topViewedMovies',
                'topFavoriteMovies'
            )
        );
    }
}