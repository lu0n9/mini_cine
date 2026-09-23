<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => ['required', 'exists:movies,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);

        Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        $ratingQuery = Rating::where('movie_id', $movie->id);

        $ratingCount = $ratingQuery->count();

        $ratingAverage = $ratingCount > 0
            ? round($ratingQuery->avg('rating') * 2, 1)
            : 0;

        $ratingPercentages = [];

        for ($star = 5; $star >= 1; $star--) {
            $count = (clone $ratingQuery)
                ->where('rating', $star)
                ->count();

            $ratingPercentages[$star] = $ratingCount > 0
                ? round(($count / $ratingCount) * 100)
                : 0;
        }

        $movie->update([
            'rating' => $ratingAverage,
            'rating_count' => $ratingCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá phim thành công.',
            'rating_average' => $ratingAverage,
            'rating_count' => $ratingCount,
            'rating_percentages' => $ratingPercentages,
        ]);
    }
}