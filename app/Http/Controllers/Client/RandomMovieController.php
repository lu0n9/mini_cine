<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Movie;

class RandomMovieController extends Controller
{
    public function __invoke()
    {
        $publishedMovies = Movie::query()->where('is_published', true);

        // Combine the highest-viewed movies with well-rated movies, then
        // choose randomly from that candidate pool.
        $mostViewedIds = (clone $publishedMovies)
            ->has('views')
            ->withCount('views')
            ->orderByDesc('views_count')
            ->limit(40)
            ->pluck('id');

        $wellRatedIds = (clone $publishedMovies)
            ->whereHas('ratings', fn ($query) => $query->where('rating', '>=', 4))
            ->orderByDesc('rating')
            ->orderByDesc('rating_count')
            ->limit(40)
            ->pluck('id');

        $candidateIds = $mostViewedIds->merge($wellRatedIds)->unique()->values();

        if ($candidateIds->isEmpty()) {
            $candidateIds = (clone $publishedMovies)->pluck('id');
        }

        $movies = Movie::query()
            ->whereIn('id', $candidateIds)
            ->with('genres')
            ->withCount(['views', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('client.pages.random-movies', compact('movies'));
    }
}
