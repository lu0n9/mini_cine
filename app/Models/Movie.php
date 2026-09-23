<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';

    protected $fillable = [
        'title',
        'slug',
        'original_title',
        'description',
        'short_description',

        'poster',
        'thumbnail',
        'backdrop',

        'type',
        'release_year',
        'release_date',
        'duration',
        'quality',
        'language',

        'imdb_id',
        'imdb_rating',
        'tmdb_id',

        'trailer_url',
        'tags',

        'status',
        'is_published',

        'featured',
        'popular',
        'recommended',

        'seo_title',
        'canonical_url',
        'seo_description',
        'seo_keywords',
        'og_title',
        'og_image',
        'og_description',
    ];

    protected $casts = [
    'release_year' => 'integer',
    'release_date' => 'date',
    'duration' => 'integer',
    'imdb_rating' => 'decimal:1',
    'tmdb_id' => 'integer',

    'is_published' => 'boolean',
    'featured' => 'boolean',
    'popular' => 'boolean',
    'recommended' => 'boolean',
];

    /*
    |--------------------------------------------------------------------------
    | Genres
    |--------------------------------------------------------------------------
    */

    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'movie_genre',
            'movie_id',
            'genre_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    public function countries()
    {
        return $this->belongsToMany(
            Country::class,
            'movie_country',
            'movie_id',
            'country_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | People
    |--------------------------------------------------------------------------
    */

    public function people()
    {
        return $this->belongsToMany(
            Person::class,
            'movie_people',
            'movie_id',
            'person_id'
        )->withPivot('role');
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function watchHistories()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function views()
    {
        return $this->hasMany(MovieView::class);
    }

    public function sources()
    {
        return $this->hasMany(MovieSource::class);
    }
    
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'movie_tag',
            'movie_id',
            'tag_id'
        )->withTimestamps();
    }

    public function videoProcessingJobs(): HasMany
    {
        return $this->hasMany(VideoProcessingJob::class);
    }
     public function collections(): BelongsToMany
    {
        return $this->belongsToMany(
            Collection::class,
            'collection_movie',
            'movie_id',
            'collection_id'
        )
        ->withPivot('sort_order')
        ->withTimestamps();
    }

    public function scopeRecommendForUser($query, User $user, int $limit = 10)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Thể loại User thích
        |--------------------------------------------------------------------------
        */

        $favoriteGenreIds = DB::table('watch_histories')
            ->join(
                'movie_genre',
                'watch_histories.movie_id',
                '=',
                'movie_genre.movie_id'
            )
            ->where('watch_histories.user_id', $user->id)
            ->select(
                'movie_genre.genre_id',
                DB::raw('COUNT(watch_histories.id) as total_watch')
            )
            ->groupBy('movie_genre.genre_id')
            ->orderByDesc('total_watch')
            ->limit(5)
            ->pluck('genre_id');


        /*
        |--------------------------------------------------------------------------
        | 2. Người User thích
        |--------------------------------------------------------------------------
        |
        | Dựa trên các phim User đã xem.
        |
        */

        $favoritePeopleIds = DB::table('watch_histories')
            ->join(
                'movie_people',
                'watch_histories.movie_id',
                '=',
                'movie_people.movie_id'
            )
            ->where('watch_histories.user_id', $user->id)
            ->select(
                'movie_people.person_id',
                DB::raw('COUNT(watch_histories.id) as total_watch')
            )
            ->groupBy('movie_people.person_id')
            ->orderByDesc('total_watch')
            ->limit(10)
            ->pluck('person_id');


        /*
        |--------------------------------------------------------------------------
        | 3. Quốc gia User thích
        |--------------------------------------------------------------------------
        */

        $favoriteCountryIds = DB::table('watch_histories')
            ->join(
                'movie_country',
                'watch_histories.movie_id',
                '=',
                'movie_country.movie_id'
            )
            ->where('watch_histories.user_id', $user->id)
            ->select(
                'movie_country.country_id',
                DB::raw('COUNT(watch_histories.id) as total_watch')
            )
            ->groupBy('movie_country.country_id')
            ->orderByDesc('total_watch')
            ->limit(5)
            ->pluck('country_id');


        /*
        |--------------------------------------------------------------------------
        | 4. Loại bỏ phim User đã xem
        |--------------------------------------------------------------------------
        */

        $watchedMovieIds = $user->watchHistories()
            ->whereNotNull('movie_id')
            ->pluck('movie_id');


        /*
        |--------------------------------------------------------------------------
        | 5. Bắt đầu tính điểm
        |--------------------------------------------------------------------------
        */

        $movies = $query
            ->where('is_published', true)
            ->whereNotIn('movies.id', $watchedMovieIds)

            ->with([
                'genres',
                'countries',
                'people',
            ])

            ->get();


        /*
        |--------------------------------------------------------------------------
        | 6. Tính Recommendation Score
        |--------------------------------------------------------------------------
        */

        $movies->each(function ($movie) use (
            $favoriteGenreIds,
            $favoritePeopleIds,
            $favoriteCountryIds,
            $user
        ) {

            $score = 0;


            /*
            |--------------------------------------------------------------------------
            | Genre
            |--------------------------------------------------------------------------
            */

            $genreIds = $movie->genres
                ->pluck('id');

            $genreMatches = $genreIds
                ->intersect($favoriteGenreIds)
                ->count();

            $score += $genreMatches * 35;


            /*
            |--------------------------------------------------------------------------
            | People
            |--------------------------------------------------------------------------
            */

            $peopleIds = $movie->people
                ->pluck('id');

            $peopleMatches = $peopleIds
                ->intersect($favoritePeopleIds)
                ->count();

            $score += $peopleMatches * 25;


            /*
            |--------------------------------------------------------------------------
            | Country
            |--------------------------------------------------------------------------
            */

            $countryIds = $movie->countries
                ->pluck('id');

            $countryMatches = $countryIds
                ->intersect($favoriteCountryIds)
                ->count();

            $score += $countryMatches * 15;


            /*
            |--------------------------------------------------------------------------
            | Rating của User
            |--------------------------------------------------------------------------
            |
            | Nếu User từng rating cao các phim cùng genre,
            | tăng điểm recommendation.
            |
            */

            $userRating = $user->ratings()
                ->where('movie_id', $movie->id)
                ->value('rating');

            if ($userRating) {

                if ($userRating >= 8) {
                    $score += 15;
                } elseif ($userRating >= 6) {
                    $score += 5;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Popular
            |--------------------------------------------------------------------------
            */

            if ($movie->popular) {
                $score += 10;
            }


            /*
            |--------------------------------------------------------------------------
            | Recommended
            |--------------------------------------------------------------------------
            */

            if ($movie->recommended) {
                $score += 10;
            }


            /*
            |--------------------------------------------------------------------------
            | Featured
            |--------------------------------------------------------------------------
            */

            if ($movie->featured) {
                $score += 5;
            }


            /*
            |--------------------------------------------------------------------------
            | Lưu score tạm thời
            |--------------------------------------------------------------------------
            */

            $movie->recommendation_score = $score;
        });


        /*
        |--------------------------------------------------------------------------
        | 7. Sort theo điểm
        |--------------------------------------------------------------------------
        */

        return $movies
            ->sortByDesc('recommendation_score')
            ->take($limit)
            ->values();
    }
   
}