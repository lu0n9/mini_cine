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
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'max:50'],
            'genre' => ['nullable', 'integer', 'exists:genres,id'],
            'country' => ['nullable', 'integer', 'exists:countries,id'],
            'year' => ['nullable', 'integer', 'min:1888', 'max:2200'],
            'language' => ['nullable', 'in:vietsub,thuyet_minh,long_tieng'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'sort' => ['nullable', 'in:newest,oldest,rating,views,name'],
        ]);

        $query = Movie::published()
            ->with(['genres', 'countries'])
            ->withCount(['episodes', 'views']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($movieQuery) use ($search) {
                $movieQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('original_title', 'like', "%{$search}%")
                    ->orWhereHas('people', fn ($peopleQuery) => $peopleQuery->where('people.name', 'like', "%{$search}%"))
                    ->orWhereHas('genres', fn ($genreQuery) => $genreQuery->where('genres.name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['genre'])) {
            $query->whereHas('genres', fn ($genreQuery) => $genreQuery->where('genres.id', $filters['genre']));
        }

        if (!empty($filters['country'])) {
            $query->whereHas('countries', fn ($countryQuery) => $countryQuery->where('countries.id', $filters['country']));
        }

        if (!empty($filters['year'])) {
            $query->where('release_year', $filters['year']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (isset($filters['rating']) && $filters['rating'] !== '') {
            $query->where('imdb_rating', '>=', $filters['rating']);
        }

        switch ($filters['sort'] ?? 'newest') {
            case 'oldest':
                $query->orderBy('release_year')->orderBy('created_at');
                break;
            case 'rating':
                $query->orderByDesc('imdb_rating')->orderByDesc('rating');
                break;
            case 'views':
                $query->orderByDesc('views_count');
                break;
            case 'name':
                $query->orderBy('title');
                break;
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $movies = $query->paginate(24)->withQueryString();
        $genres = Genre::orderBy('name')->get(['id', 'name']);
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $years = Movie::published()->whereNotNull('release_year')->distinct()->orderByDesc('release_year')->pluck('release_year');
        $types = Movie::published()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type');
        $languages = Movie::published()->whereNotNull('language')->distinct()->orderBy('language')->pluck('language');

        return view('client.pages.movies.movies', compact('movies', 'genres', 'countries', 'years', 'types', 'languages'));
    }

    public function movieDetail($slug) {
        $movie = Movie::where('slug', $slug)
        ->where('is_published', true)
        ->with([
            'genres',
            'countries',
            'people',
            'ratings',
            'favorites',
            'seasons' => function ($query) {
                $query->orderBy('season_number')->with([
                    'episodes' => function ($episodeQuery) {
                        $episodeQuery->orderBy('episode_number');
                    },
                ]);
            },
            'episodes' => function ($query) {
                $query->orderBy('season_id')->orderBy('episode_number')->with('subtitles');
            },

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
                        ->orderBy('season_id', 'asc')
                        ->orderBy('episode_number', 'asc')
                        ->with([
                            'season',
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
        $requestedEpisodeId = (int) $request->query('episode', 0);
        $currentEpisode = $requestedEpisodeId
            ? $movie->episodes->firstWhere('id', $requestedEpisodeId)
            : null;

        // Keep supporting old episode-number links; new links use episode ID
        // because episode numbers repeat between seasons.
        if (!$currentEpisode && $request->filled('ep')) {
            $currentEpisode = $movie->episodes->firstWhere('episode_number', (int) $request->query('ep'));
        }

        // Nếu không tìm thấy tập được yêu cầu
        // thì lấy tập đầu tiên
        if (!$currentEpisode) {
            $currentEpisode = $movie->episodes->first();
        }

        if (!$currentEpisode) {
            abort(404, 'Phim chưa có tập để xem.');
        }

        $hasPremiumAccess = auth()->check() && auth()->user()->hasPremiumAccess();
        $episodeList = $movie->episodes->sortBy(fn ($episode) => sprintf(
            '%08d-%08d',
            $episode->season?->season_number ?? 0,
            $episode->episode_number
        ))->values();
        $episodePosition = $episodeList->search(fn ($episode) => $episode->id === $currentEpisode->id);
        $nextEpisode = $episodePosition !== false ? $episodeList->get($episodePosition + 1) : null;
        $knownEpisodes = $episodeList->filter(fn ($episode) => (int) $episode->duration > 0);
        $totalDuration = $knownEpisodes->isNotEmpty()
            ? (int) $knownEpisodes->sum('duration')
            : ((int) $movie->duration * 60);
        $freeWatchLimit = $totalDuration > 0 ? (int) floor($totalDuration * 0.10) : null;
        $episodeOffset = 0;
        foreach ($episodeList as $episode) {
            if ($episode->id === $currentEpisode->id) break;
            $episodeOffset += (int) $episode->duration;
        }
        $watchLimitSeconds = $hasPremiumAccess || $freeWatchLimit === null
            ? null
            : max(0, (int) $currentEpisode->duration > 0
                ? min((int) $currentEpisode->duration, $freeWatchLimit - $episodeOffset)
                : $freeWatchLimit - $episodeOffset);


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
        if (!$hasPremiumAccess && $watchLimitSeconds !== null) {
            $savedWatchTime = min($savedWatchTime, $watchLimitSeconds);
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
                'savedWatchTime',
                'hasPremiumAccess',
                'watchLimitSeconds',
                'nextEpisode'
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

        $episode = Episode::whereKey($validated['episode_id'])
            ->where('movie_id', $validated['movie_id'])
            ->firstOrFail();
        $user = $request->user();
        if (!$user->hasPremiumAccess()) {
            $movie = Movie::with('episodes.season')->findOrFail($validated['movie_id']);
            $episodeList = $movie->episodes->sortBy(fn ($item) => sprintf(
                '%08d-%08d',
                $item->season?->season_number ?? 0,
                $item->episode_number
            ))->values();
            $knownEpisodes = $episodeList->filter(fn ($item) => (int) $item->duration > 0);
            $total = $knownEpisodes->isNotEmpty() ? (int) $knownEpisodes->sum('duration') : (int) $movie->duration * 60;
            if ($total > 0) {
                $cutoff = (int) floor($total * 0.10);
                $offset = 0;
                foreach ($episodeList as $item) {
                    if ($item->id === $episode->id) break;
                    $offset += (int) $item->duration;
                }
                $limit = max(0, (int) $episode->duration > 0
                    ? min((int) $episode->duration, $cutoff - $offset)
                    : $cutoff - $offset);
                $validated['watch_time'] = min($validated['watch_time'], $limit);
                $validated['duration'] = $limit;
            }
        }

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
