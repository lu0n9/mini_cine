<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Country;
use App\Models\Genre;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Session;
use App\Models\MovieView;

class MovieController extends Controller
{
    public function movieDetail($slug) {
        $movie = Movie::where('slug', $slug)
        ->where('is_published', true)
        ->with([
            'genres',
            'countries',
            'people',
            'ratings',

            'comments' => function ($query) {
                $query
                    ->whereNull('parent_id')
                    ->where('is_approved', true)
                    ->with([
                        'user',

                        'likes',

                        'replies' => function ($replyQuery) {
                            $replyQuery
                                ->where('is_approved', true)
                                ->with([
                                    'user',
                                    'likes',
                                ])
                                ->latest();
                        },
                    ])
                    ->latest();
            },
        ])

        ->firstOrFail();

        $similarMovies = Movie::where('is_published', true)
        ->where('id', '!=', $movie->id)
        ->whereHas('genres', function ($query) use ($movie) {
            $query->whereIn(
                'genres.id',
                $movie->genres->pluck('id')
            );
        })
        ->with('genres')
        ->withCount('ratings')
        ->orderByDesc('rating')
        ->limit(4)
        ->get();

        $ratings = $movie->ratings;

        $ratingCount = $ratings->count();

        $ratingAverage = $ratingCount > 0
            ? round($ratings->avg('rating'), 1)
            : 0;

        $ratingPercentages = [];

        for ($star = 5; $star >= 1; $star--) {

            $count = $ratings
                ->where('rating', $star)
                ->count();

            $ratingPercentages[$star] = $ratingCount > 0
                ? round(($count / $ratingCount) * 100)
                : 0;
        }
        $userRating = auth()->check()
        ? $ratings->firstWhere('user_id', auth()->id())
        : null;
        return view('client.pages.movies.movie-detail', compact(
        'movie',
        'similarMovies',
        'ratingCount',
        'ratingAverage',
        'ratingPercentages',
        'userRating'
    ));
    }

    public function watch(string $slug, Request $request)
    {
        // 1. Lấy phim + tập + nguồn phát + subtitle
        $movie = Movie::where('is_published', true)
            ->where('slug', $slug)
            ->with([
                'seasons',

                'episodes' => function ($query) {
                    $query
                        ->orderBy('episode_number', 'asc')
                        ->with([
                            'sources' => function ($sq) {
                                $sq->where('is_active', true);
                            },

                            'subtitles' => function ($sq) {
                                $sq->where('is_active', true)
                                ->orderByDesc('is_default');
                            },
                        ]);
                },
            ])
            ->firstOrFail();


        // 2. Xác định tập hiện tại
        $currentEpNumber = (int) $request->query('ep', 1);

        $currentEpisode = $movie->episodes
            ->firstWhere('episode_number', $currentEpNumber);

        // Nếu không tìm thấy tập được yêu cầu
        // thì lấy tập đầu tiên
        if (!$currentEpisode) {
            $currentEpisode = $movie->episodes->first();
        }

        if (!$currentEpisode) {
            abort(404, 'Phim chưa có tập để xem.');
        }


        // 3. Lấy source đang active
        $activeSource = $currentEpisode->sources->first();

        $streamUrl = $activeSource
            ? $activeSource->source_url
            : null;


        // 4. Lấy subtitle của tập hiện tại
        $subtitles = $currentEpisode->subtitles;


        // 5. Lấy lịch sử xem dở
        $savedWatchTime = 0;

        if (auth()->check()) {

            $history = WatchHistory::where(
                    'user_id',
                    auth()->id()
                )
                ->where(
                    'episode_id',
                    $currentEpisode->id
                )
                ->first();

            if ($history) {
                $savedWatchTime = $history->watch_time;
            }
        }


        // 6. Ghi nhận lượt xem phim
        $viewSessionKey = 'viewed_movie_' . $movie->id;

        if (!Session::has($viewSessionKey)) {

            MovieView::create([
                'movie_id'   => $movie->id,
                'user_id'    => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Session::put($viewSessionKey, true);
        }


        // 7. Trả dữ liệu sang Blade
        return view(
            'client.pages.movies.watch',
            compact(
                'movie',
                'currentEpisode',
                'streamUrl',
                'subtitles',
                'savedWatchTime'
            )
        );
    }

    public function saveHistory(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'movie_id'   => 'required|integer|exists:movies,id',
            'episode_id' => 'required|integer|exists:episodes,id',
            'watch_time' => 'required|integer|min:0',
            'duration'   => 'nullable|integer|min:0',
        ]);

        try {
            // Đưa cả movie_id vào mảng điều kiện tìm kiếm để đảm bảo tính duy nhất
            $history = WatchHistory::updateOrCreate(
                [
                    'user_id'    => auth()->id(),
                    'movie_id'   => $validated['movie_id'],
                    'episode_id' => $validated['episode_id'],
                ],
                [
                    'watch_time'      => $validated['watch_time'],
                    'duration'        => $validated['duration'] ?? 0,
                    'last_watched_at' => now(),
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Đã lưu tiến trình',
                'data'    => $history
            ]);
        } catch (Throwable $e) {
            // Ghi log chi tiết lỗi ra storage/logs/laravel.log để dễ debug
            \Log::error('Save Watch History Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showGenre(Request $request, string $slug)
    {
        $genre = Genre::where('slug', $slug)
            ->firstOrFail();

        $query = Movie::published()
            ->with([
                'genres',
                'countries',
                'episodes',
            ])
            ->whereHas('genres', function ($query) use ($genre) {
                $query->where(
                    'genres.id',
                    $genre->id
                );
            });

        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($genreQuery) use ($request) {
                $genreQuery->where(
                    'genres.id',
                    $request->genre
                );
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('countries', function ($countryQuery) use ($request) {
                $countryQuery->where(
                    'countries.id',
                    $request->country
                );
            });
        }

        if ($request->filled('year')) {
            $query->where(
                'release_year',
                $request->year
            );
        }

        if ($request->filled('imdb')) {
            $query->where(
                'imdb_rating',
                '>=',
                $request->imdb
            );
        }

        switch ($request->get('sort')) {
            case 'oldest':
                $query->orderBy(
                    'release_year',
                    'asc'
                );
                break;

            case 'rating':
                $query->orderByDesc('imdb_rating');
                break;

            case 'views':
                $query->withCount('views')
                    ->orderByDesc('views_count');
                break;

            case 'name':
                $query->orderBy(
                    'title',
                    'asc'
                );
                break;

            default:
                $query->orderByDesc('created_at');
                break;
        }

        $movies = $query
            ->paginate(12)
            ->withQueryString();

        $genres = Genre::orderBy('name')
            ->get();

        $countries = Country::orderBy('name')
            ->get();

        $years = Movie::published()
            ->whereNotNull('release_year')
            ->distinct()
            ->orderByDesc('release_year')
            ->pluck('release_year');

        $types = Movie::published()
            ->whereNotNull('type')
            ->distinct()
            ->orderByDesc('type')
            ->pluck('type');

        return view(
            'client.pages.movies.show_genre',
            compact(
                'genre',
                'movies',
                'genres',
                'countries',
                'years',
                'types'
            )
        );
    }

    public function history(){
        $history = collect();

        if (auth()->check()) {
            $history = WatchHistory::where(
                'user_id',
                auth()->id()
            )
                ->with(['movie', 'episode'])
                ->orderByDesc('updated_at')
                ->take(4)
                ->get();
        }

        return view('client.pages.movies.history',compact('history'));
    }
}