<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\MovieView;
use App\Models\PremiumTransaction;
use App\Models\Rating;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function dashboard()
    {
        $now = now();
        $totalMovies = Movie::whereNull('deleted_at')->count();
        $totalEpisodes = Episode::count();
        $totalUsers = User::where('role', 'user')->count();
        $viewsToday = MovieView::whereDate('viewed_at', $now->toDateString())->count();

        $moviesThisMonth = Movie::whereNull('deleted_at')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count();
        $episodesThisWeek = Episode::whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->count();
        $usersThisMonth = User::where('role', 'user')->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->count();
        $viewsYesterday = MovieView::whereDate('viewed_at', $now->copy()->subDay()->toDateString())->count();
        $viewsChange = $viewsYesterday > 0
            ? round((($viewsToday - $viewsYesterday) / $viewsYesterday) * 100)
            : ($viewsToday > 0 ? 100 : 0);

        $monthlyViewRows = DB::table('movie_views')
            ->selectRaw('MONTH(viewed_at) as month, COUNT(*) as total')
            ->whereYear('viewed_at', $now->year)
            ->groupBy(DB::raw('MONTH(viewed_at)'))
            ->pluck('total', 'month');
        $monthlyViews = collect(range(1, 12))->map(function ($month) use ($monthlyViewRows) {
            return [
                'label' => 'T' . $month,
                'total' => (int) ($monthlyViewRows[$month] ?? 0),
            ];
        });
        $maxMonthlyViews = max(1, (int) $monthlyViews->max('total'));
        $monthlyViews = $monthlyViews->map(function ($month) use ($maxMonthlyViews) {
            $month['height'] = $month['total'] > 0
                ? max(3, (int) round($month['total'] / $maxMonthlyViews * 100))
                : 0;
            return $month;
        });

        $activities = collect();
        Movie::whereNull('deleted_at')->latest()->limit(3)->get(['id', 'title', 'created_at'])->each(function ($movie) use ($activities) {
            $activities->push([
                'icon' => '+', 'title' => 'Phim mới',
                'description' => '“' . $movie->title . '” đã được thêm',
                'created_at' => $movie->created_at,
            ]);
        });
        Rating::with(['user:id,name', 'movie:id,title'])->latest()->limit(3)->get()->each(function ($rating) use ($activities) {
            $activities->push([
                'icon' => '★', 'title' => 'Đánh giá mới',
                'description' => ($rating->rating ?? 0) . '★ cho “' . ($rating->movie?->title ?? 'Phim đã xóa') . '” · ' . ($rating->user?->name ?? 'Người dùng'),
                'created_at' => $rating->created_at,
            ]);
        });
        Report::with('movie:id,title')->latest()->limit(3)->get()->each(function ($report) use ($activities) {
            $activities->push([
                'icon' => '!', 'title' => 'Báo lỗi',
                'description' => ($report->movie?->title ?? 'Phim') . ' · ' . str_replace('_', ' ', $report->type),
                'created_at' => $report->created_at,
            ]);
        });
        Episode::with('movie:id,title')->latest()->limit(3)->get()->each(function ($episode) use ($activities) {
            $activities->push([
                'icon' => '▶', 'title' => 'Episode mới',
                'description' => 'Tập ' . $episode->episode_number . ' · ' . ($episode->movie?->title ?? 'Phim'),
                'created_at' => $episode->created_at,
            ]);
        });
        PremiumTransaction::with('subscription.user:id,name', 'subscription.plan:id,name,billing_period')
            ->latest()->limit(3)->get()->each(function ($transaction) use ($activities) {
                $activities->push([
                    'icon' => '₫', 'title' => 'Thanh toán ' . ($transaction->status === 'paid' ? 'thành công' : $transaction->status),
                    'description' => ($transaction->subscription?->plan?->name ?? 'Premium') . ' · ₫' . number_format($transaction->amount, 0, ',', '.') . ' · ' . ($transaction->subscription?->user?->name ?? 'Người dùng'),
                    'created_at' => $transaction->paid_at ?? $transaction->created_at,
                ]);
            });
        $activities = $activities->sortByDesc('created_at')->take(6)->values();

        $popularMovies = Movie::whereNull('deleted_at')->where('is_published', true)->with('genres:id,name')
            ->withCount('views')
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('admin.pages.dashboard.index', compact(
            'totalMovies', 'totalEpisodes', 'totalUsers', 'viewsToday',
            'moviesThisMonth', 'episodesThisWeek', 'usersThisMonth', 'viewsChange',
            'monthlyViews', 'activities', 'popularMovies'
        ));
    }
}
