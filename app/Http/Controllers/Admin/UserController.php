<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\UserBannedMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->where('is_active', true),

                'banned' => $query->where('is_active', false),

                'unverified' => $query->whereNull('email_verified_at'),

                default => null,
            };
        }

        $users = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.pages.user.users',
            compact('users')
        );
    }

   public function show(Request $request, User $user)
    {
        $tab = $request->get('tab', 'overview');

        /*
        |--------------------------------------------------------------------------
        | Tổng thời gian xem
        |--------------------------------------------------------------------------
        */

        $totalWatchTime = $user->watchHistories()
            ->sum('watch_time');


        /*
        |--------------------------------------------------------------------------
        | Tổng phim đã xem
        |--------------------------------------------------------------------------
        */

        $totalMoviesWatched = $user->watchHistories()
            ->whereNotNull('movie_id')
            ->distinct('movie_id')
            ->count('movie_id');


        /*
        |--------------------------------------------------------------------------
        | Tổng lượt xem
        |--------------------------------------------------------------------------
        */

        $totalViews = $user->movieViews()->count();


        /*
        |--------------------------------------------------------------------------
        | Tổng Comment
        |--------------------------------------------------------------------------
        */

        $totalComments = $user->comments()->count();


        /*
        |--------------------------------------------------------------------------
        | Tổng Rating
        |--------------------------------------------------------------------------
        */

        $totalRatings = $user->ratings()->count();


        /*
        |--------------------------------------------------------------------------
        | Tổng Favorite
        |--------------------------------------------------------------------------
        */

        $totalFavorites = $user->favorites()->count();


        /*
        |--------------------------------------------------------------------------
        | Tổng Watchlist
        |--------------------------------------------------------------------------
        */

        $totalWatchlists = $user->watchlists()->count();


        /*
        |--------------------------------------------------------------------------
        | Thể loại yêu thích
        |--------------------------------------------------------------------------
        */

        $favoriteGenres = \App\Models\Genre::query()
            ->select(
                'genres.id',
                'genres.name',
                DB::raw('COUNT(watch_histories.id) as watch_count')
            )
            ->join(
                'movie_genre',
                'genres.id',
                '=',
                'movie_genre.genre_id'
            )
            ->join(
                'movies',
                'movie_genre.movie_id',
                '=',
                'movies.id'
            )
            ->join(
                'watch_histories',
                'movies.id',
                '=',
                'watch_histories.movie_id'
            )
            ->where(
                'watch_histories.user_id',
                $user->id
            )
            ->groupBy(
                'genres.id',
                'genres.name'
            )
            ->orderByDesc('watch_count')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dữ liệu theo tab
        |--------------------------------------------------------------------------
        */

        $recentActivities = collect();

        $favorites = collect();

        $comments = collect();

        $ratings = collect();

        $devices = collect();

        $notifications = collect();


        /*
        |--------------------------------------------------------------------------
        | Hoạt động
        |--------------------------------------------------------------------------
        */

        if ($tab === 'activity') {

            $recentActivities = $user->watchHistories()
                ->with([
                    'movie',
                    'episode',
                ])
                ->orderByDesc('last_watched_at')
                ->paginate(20)
                ->withQueryString();
        }


        /*
        |--------------------------------------------------------------------------
        | Phim yêu thích
        |--------------------------------------------------------------------------
        */

        if ($tab === 'favorites') {

            $favorites = $user->favorites()
                ->with('movie')
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();
        }


        /*
        |--------------------------------------------------------------------------
        | Comment
        |--------------------------------------------------------------------------
        */

        if ($tab === 'comments') {

            $comments = $user->comments()
                ->with('movie')
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();
        }


        /*
        |--------------------------------------------------------------------------
        | Rating
        |--------------------------------------------------------------------------
        */

        if ($tab === 'ratings') {

            $ratings = $user->ratings()
                ->with('movie')
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();
        }


        /*
        |--------------------------------------------------------------------------
        | Thiết bị / IP
        |--------------------------------------------------------------------------
        */

        if ($tab === 'devices') {

            $devices = $user->movieViews()
                ->select(
                    'ip_address',
                    'user_agent',
                    DB::raw('MAX(created_at) as last_seen'),
                    DB::raw('COUNT(*) as total_views')
                )
                ->groupBy(
                    'ip_address',
                    'user_agent'
                )
                ->orderByDesc('last_seen')
                ->paginate(20)
                ->withQueryString();
        }


        /*
        |--------------------------------------------------------------------------
        | Thông báo
        |--------------------------------------------------------------------------
        */

        if ($tab === 'notifications') {

            $notifications = $user->notifications()
                ->orderByDesc('created_at')
                ->paginate(20)
                ->withQueryString();
        }


        return view(
            'admin.pages.user.show',
            compact(
                'user',
                'tab',
                'totalWatchTime',
                'totalMoviesWatched',
                'totalViews',
                'totalComments',
                'totalRatings',
                'totalFavorites',
                'totalWatchlists',
                'favoriteGenres',
                'recentActivities',
                'favorites',
                'comments',
                'ratings',
                'devices',
                'notifications'
            )
        );
    }


    public function ban(Request $request, User $user)
    {
        // Không cho ban Admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể ban tài khoản Admin.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'duration' => [
                'required',
                'in:1_day,3_days,7_days,30_days,permanent',
            ],

            'reason' => [
                'required',
                'string',
                'max:5000',
            ],
        ], [
            'duration.required' => 'Vui lòng chọn thời gian ban.',
            'duration.in' => 'Thời gian ban không hợp lệ.',
            'reason.required' => 'Vui lòng nhập lý do ban.',
            'reason.max' => 'Lý do ban không được vượt quá 5000 ký tự.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Kiểm tra user đã bị ban
        |--------------------------------------------------------------------------
        */

        if ($user->isBanned()) {
            return back()->with(
                'error',
                'Tài khoản này hiện đang bị khóa.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Tính thời gian hết hạn
        |--------------------------------------------------------------------------
        */

        $banExpiresAt = match ($validated['duration']) {
            '1_day' => now()->addDay(),

            '3_days' => now()->addDays(3),

            '7_days' => now()->addDays(7),

            '30_days' => now()->addDays(30),

            'permanent' => null,
        };


        /*
        |--------------------------------------------------------------------------
        | Ban user
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $user,
            $validated,
            $banExpiresAt
        ) {
            $user->update([
                'is_active' => false,
                'ban_reason' => $validated['reason'],
                'ban_expires_at' => $banExpiresAt,
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Queue email
        |--------------------------------------------------------------------------
        |
        | Không dùng send().
        | queue() sẽ đưa email vào bảng jobs.
        |
        */

        Mail::to($user->email)
            ->queue(new UserBannedMail($user));


        return back()->with(
            'success',
            'Đã ban user. Email thông báo đã được đưa vào hàng đợi gửi.'
        );
    }


    public function unban(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with(
                'error',
                'Tài khoản Admin không cần mở ban.'
            );
        }

        $user->activateFromBan();

        return back()->with(
            'success',
            'Đã mở khóa tài khoản.'
        );
    }
    public function permissions()
    {
        return view('admin.pages.user.permissions');
    }
    public function banned()
    {
        return view('admin.pages.user.banned');
    }
}
