<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Person;
use App\Models\MovieView;
use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $continueWatching = collect();
        $user = auth()->user();

        if ($user) {
            $continueWatching = WatchHistory::where('user_id', auth()->id())
            ->whereNotNull('episode_id')
            ->whereHas('episode')
            ->with([
                'movie',
                'episode.season',
            ])
            ->latest('last_watched_at')
            ->get();
        }
        

        $recommendedMovies = collect();

        $hasRecommendationData = false;

        if ($user) {

            $preferences = session('recommendation_preferences');

            if ($preferences) {

                $hasRecommendationData =
                    !empty($preferences['genres']) ||
                    !empty($preferences['countries']) ||
                    !empty($preferences['people']);

                $genres = $preferences['genres'] ?? [];
                $countries = $preferences['countries'] ?? [];
                $people = $preferences['people'] ?? [];

                $recommendedMovies = Movie::query()
                    ->where('is_published', true)
                    ->where(function ($query) use (
                        $genres,
                        $countries,
                        $people
                    ) {

                        if (!empty($genres)) {
                            $query->whereHas('genres', function ($q) use ($genres) {
                                $q->whereIn('genres.id', $genres);
                            });
                        }

                        if (!empty($countries)) {
                            $query->orWhereHas('countries', function ($q) use ($countries) {
                                $q->whereIn('countries.id', $countries);
                            });
                        }

                        if (!empty($people)) {
                            $query->orWhereHas('people', function ($q) use ($people) {
                                $q->whereIn('people.id', $people);
                            });
                        }
                    })
                    ->with([
                        'genres',
                        'countries',
                        'people',
                    ])
                    ->withCount([
                        'episodes',
                        'seasons',
                    ])
                    ->orderByDesc('popular')
                    ->orderByDesc('rating')
                    ->limit(6)
                    ->get();
            }
        }

        // =========================
        // DỮ LIỆU CHO MODAL
        // =========================

        $genres = Genre::query()
            ->orderBy('name')
            ->get();

        $countries = Country::query()
            ->orderBy('name')
            ->get();

        $people = Person::query()
            ->orderBy('name')
            ->get();

        $proposeMovie = Movie::published()
            ->where('recommended', true)
            ->with('genres')
            ->orderByDesc('rating')
            ->first();

        $trendingIds = Cache::remember(
            'weekly_trending_movie_ids',
            now()->addHours(24),
            function () {
                return Movie::published()
                    ->whereHas('views', function ($query) {
                        $query->where(
                            'created_at',
                            '>=',
                            now()->subDays(7)
                        );
                    })
                    ->withCount([
                        'views' => function ($query) {
                            $query->where(
                                'created_at',
                                '>=',
                                now()->subDays(7)
                            );
                        }
                    ])
                    ->orderByDesc('views_count')
                    ->take(10)
                    ->pluck('id')
                    ->toArray();
            }
        );

        $trendingMovies = collect();

        if (!empty($trendingIds)) {
            $trendingMovies = Movie::whereIn(
                'id',
                $trendingIds
            )
                ->with('genres')
                ->orderByRaw(
                    'FIELD(id, ' . implode(',', $trendingIds) . ')'
                )
                ->get();
        }

        $newMovies = Movie::published()
            ->with('genres')
            ->withCount(['episodes', 'seasons'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $filmGenres = Genre::withCount([
            'movies' => function ($query) {
                $query->where('is_published', true);
            }
        ])->get();

        return view(
            'client.pages.home',
            compact(
                'continueWatching',
                'proposeMovie',
                'trendingMovies',
                'newMovies',
                'filmGenres',
                'recommendedMovies',
                'hasRecommendationData',
                'genres',
                'countries',
                'people'
            )
        );
    }
}
