<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Person;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $term = trim($validated['q']);
        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $movies = Movie::published()
            ->with('genres')
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('original_title', 'like', "%{$term}%");
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'poster', 'release_year']);

        $people = Person::query()
            ->whereHas('movies', fn ($query) => $query->where('is_published', true))
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(3)
            ->get(['id', 'name', 'slug', 'avatar']);

        $genres = Genre::query()
            ->whereHas('movies', fn ($query) => $query->where('is_published', true))
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(3)
            ->get(['id', 'name', 'slug']);

        $results = $movies->map(fn (Movie $movie) => [
            'kind' => 'Phim',
            'title' => $movie->title,
            'subtitle' => trim(($movie->release_year ?: '') . ($movie->genres->isNotEmpty() ? ' · ' . $movie->genres->first()->name : '')),
            'image' => $movie->poster ? asset('storage/' . $movie->poster) : asset('images/poster-default.png'),
            'url' => route('movie.detail', $movie->slug),
        ]);

        $results = $results->concat($people->map(fn (Person $person) => [
            'kind' => 'Diễn viên',
            'title' => $person->name,
            'subtitle' => 'Diễn viên / đạo diễn',
            'image' => $person->avatar ? asset('storage/' . $person->avatar) : asset('images/poster-default.png'),
            'url' => route('people.show', $person->slug),
        ]));

        $results = $results->concat($genres->map(fn (Genre $genre) => [
            'kind' => 'Thể loại',
            'title' => $genre->name,
            'subtitle' => 'Xem phim theo thể loại',
            'image' => asset('images/poster-default.png'),
            'url' => route('genres.show', $genre->slug),
        ]));

        return response()->json(['results' => $results->values()]);
    }
}
