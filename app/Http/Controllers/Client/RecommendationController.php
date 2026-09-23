<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function savePreferences(Request $request)
    {
        $validated = $request->validate([
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],

            'countries' => ['nullable', 'array'],
            'countries.*' => ['integer', 'exists:countries,id'],

            'people' => ['nullable', 'array'],
            'people.*' => ['integer', 'exists:people,id'],
        ]);

        $genres = $validated['genres'] ?? [];
        $countries = $validated['countries'] ?? [];
        $people = $validated['people'] ?? [];

        if (
            empty($genres) &&
            empty($countries) &&
            empty($people)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một sở thích.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Lưu sở thích vào session
        |--------------------------------------------------------------------------
        */

        session([
            'recommendation_preferences' => [
                'genres' => $genres,
                'countries' => $countries,
                'people' => $people,
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tìm phim phù hợp
        |--------------------------------------------------------------------------
        */

        $recommendedMovies = Movie::query()
            ->where('is_published', true)
            ->where(function ($query) use ($genres, $countries, $people) {

                // Genre
                if (!empty($genres)) {
                    $query->whereHas('genres', function ($q) use ($genres) {
                        $q->whereIn('genres.id', $genres);
                    });
                }

                // Country
                if (!empty($countries)) {
                    $query->orWhereHas('countries', function ($q) use ($countries) {
                        $q->whereIn('countries.id', $countries);
                    });
                }

                // Actor / Director / Producer
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

        return response()->json([
            'success' => true,
            'message' => 'Đã ghi nhận sở thích của bạn.',
            'data' => [
                'genres' => $genres,
                'countries' => $countries,
                'people' => $people,
                'movie_count' => $recommendedMovies->count(),
            ],
        ]);
    }
}