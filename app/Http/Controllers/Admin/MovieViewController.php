<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\MovieView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieViewController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Tổng lượt xem
        |--------------------------------------------------------------------------
        */

        $totalViews = MovieView::count();

        $todayViews = MovieView::whereDate(
            'created_at',
            Carbon::today()
        )->count();

        $weekViews = MovieView::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();

        $monthViews = MovieView::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->count();


        /*
        |--------------------------------------------------------------------------
        | Thống kê lượt xem theo từng phim
        |--------------------------------------------------------------------------
        */

        $movieViews = Movie::query()
            ->select([
                'movies.id',
                'movies.title',
                'movies.slug',
            ])
            ->withCount('views')
            ->orderByDesc('views_count')
            ->paginate(15)
            ->withQueryString();


        return view(
            'admin.pages.statistical.views',
            compact(
                'totalViews',
                'todayViews',
                'weekViews',
                'monthViews',
                'movieViews',
            )
        );
    }

}