<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieApiController extends Controller
{
    public function home(): JsonResponse
    {
        $base = Movie::published()->with('genres:id,name');

        $featured = (clone $base)->where('featured', true)
            ->orderByDesc('created_at')->first();

        $trending = (clone $base)->orderByDesc('popular')
            ->orderByDesc('rating')->limit(12)->get();

        $latest = (clone $base)->orderByDesc('created_at')
            ->limit(12)->get();

        return response()->json(['data' => [
            'featured' => $featured ? $this->movieData($featured) : null,
            'trending' => $trending->map(fn (Movie $movie) => $this->movieData($movie))->values(),
            'latest' => $latest->map(fn (Movie $movie) => $this->movieData($movie))->values(),
        ]]);
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Movie::published()->with('genres:id,name')->orderByDesc('created_at');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('original_title', 'like', "%{$search}%"));
        }

        $movies = $query->paginate(20);

        return response()->json([
            'data' => $movies->getCollection()
                ->map(fn (Movie $movie) => $this->movieData($movie))->values(),
            'meta' => [
                'current_page' => $movies->currentPage(),
                'last_page' => $movies->lastPage(),
                'total' => $movies->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $movie = Movie::published()->where('slug', $slug)
            ->with([
                'genres:id,name',
                'countries:id,name',
                'seasons' => fn ($query) => $query->orderBy('season_number')
                    ->with(['episodes' => fn ($episodes) => $episodes->orderBy('episode_number')]),
                'episodes' => fn ($query) => $query->orderBy('season_id')
                    ->orderBy('episode_number')->with('season:id,season_number'),
            ])->firstOrFail();

        $data = $this->movieData($movie);
        $data['description'] = $movie->description;
        $data['original_title'] = $movie->original_title;
        $data['countries'] = $movie->countries->pluck('name')->values();
        $data['episodes'] = $movie->episodes->map(fn ($episode) => [
            'id' => $episode->id,
            'name' => $episode->name,
            'episode_number' => $episode->episode_number,
            'season_number' => $episode->season?->season_number,
            'duration' => $episode->duration,
        ])->values();

        return response()->json(['data' => $data]);
    }

    private function movieData(Movie $movie): array
    {
        $rating = $movie->rating ?: $movie->imdb_rating;

        return [
            'id' => $movie->id,
            'slug' => $movie->slug,
            'title' => $movie->title,
            'poster_url' => $this->mediaUrl($movie->poster),
            'backdrop_url' => $this->mediaUrl($movie->backdrop ?: $movie->thumbnail),
            'release_year' => $movie->release_year,
            'rating' => $rating !== null ? (float) $rating : null,
            'type' => $movie->type,
            'genres' => $movie->genres->pluck('name')->values(),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $disk = Storage::disk('public');

        if (config('filesystems.disks.public.driver') === 'local') {
            return request()->getSchemeAndHttpHost().'/storage/'.ltrim($path, '/');
        }

        return $disk->url($path);
    }
}
