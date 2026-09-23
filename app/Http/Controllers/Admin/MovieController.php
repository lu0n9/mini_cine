<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Collection;
use App\Models\Country;
use App\Models\Person;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index(Request $request)
    {
       $query = Movie::query()
            ->with('genres')
            ->withCount('views') // Tự động tạo thuộc tính views_count
            ->withAvg('ratings', 'rating'); // Tự động tạo thuộc tính ratings_avg_rating

        // Tìm kiếm theo tên phim hoặc slug
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo loại phim (single, series, anime, tvshow...)
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $movies = $query->latest()->paginate(10);
        return view('admin.pages.movie.movies', compact('movies'));
    }


    // Chuyển đổi nhanh trạng thái is_published
    public function toggleStatus($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->is_published = !$movie->is_published;
        $movie->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái phim!');
    }

    public function destroy($id)
    {
        // 1. Tìm phim theo ID, nếu không thấy sẽ tự động trả về lỗi 404
        $movie = Movie::findOrFail($id);

        // 2. Xóa các file ảnh liên quan trong thư mục storage nếu có
        if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
            Storage::disk('public')->delete($movie->poster);
        }

        if ($movie->backdrop && Storage::disk('public')->exists($movie->backdrop)) {
            Storage::disk('public')->delete($movie->backdrop);
        }

        // 3. Xóa liên kết ở các bảng trung gian (movie_genre, movie_country, movie_people)
        // Nếu trong Migration bạn cài ON DELETE CASCADE thì có thể bỏ qua bước detach này
        $movie->genres()->detach();

        // 4. Thực hiện xóa bản ghi Phim
        $movie->delete();

        // 5. Chuyển hướng quay lại danh sách kèm thông báo thành công
        return redirect()->back()->with('success', 'Đã xóa phim thành công!');
    }


    private function getEnumValues($table, $column)
    {
        $columnInfo = DB::selectOne(
            "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'"
        );

        if (!$columnInfo || !isset($columnInfo->Type)) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $columnInfo->Type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        return str_getcsv($matches[1], ',', "'");
    }

    public function add()
    {
        $countries = Country::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        $people = Person::orderBy('name')->get();

        $collections = Collection::where('is_active', true)
            ->orderBy('name')
            ->get();

        $tags = Tag::orderBy('name')->get();

        $types = $this->getEnumValues('movies', 'type');
        $qualities = $this->getEnumValues('movies', 'quality');
        $languages = $this->getEnumValues('movies', 'language');
        $statuses = $this->getEnumValues('movies', 'status');

        return view('admin.pages.movie.add', compact(
            'countries',
            'genres',
            'people',
            'collections',
            'tags',
            'types',
            'qualities',
            'languages',
            'statuses'
        ));
    }


   public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'original_title'    => ['nullable', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255'],
            'type'              => ['required', 'in:single,series'], // enum('single', 'series')
            'release_year'      => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'release_date'      => ['nullable', 'date'],
            'duration'          => ['nullable', 'integer', 'min:0'],
            'quality'           => ['required', 'in:HD,Full HD,2K,4K'], // enum('HD', 'Full HD', '2K', '4K')
            'language'          => ['required', 'in:vietsub,thuyet_minh,long_tieng'], // enum(...)
            'imdb_id'           => ['nullable', 'string', 'max:50'],
            'imdb_rating'       => ['nullable', 'numeric', 'min:0', 'max:10'],
            'tmdb_id'           => ['nullable', 'integer'],
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['integer', 'exists:tags,id'],
            'collections'       => ['nullable', 'array'],
            'collections.*'     => ['integer', 'exists:collections,id'],
            'short_description' => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'trailer_url'       => ['nullable', 'url', 'max:255'],
            'status'            => ['required', 'in:draft,ongoing,completed'], // enum('draft', 'ongoing', 'completed')
            'save_type'         => ['required', 'in:draft,publish'],
            'featured'          => ['nullable', 'boolean'],
            'popular'           => ['nullable', 'boolean'],
            'recommended'       => ['nullable', 'boolean'],
            'poster'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'thumbnail'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'backdrop'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'seo_title'         => ['nullable', 'string', 'max:255'],
            'canonical_url'     => ['nullable', 'url', 'max:500'],
            'seo_description'   => ['nullable', 'string'],
            'seo_keywords'      => ['nullable', 'string'],
            'og_title'          => ['nullable', 'string', 'max:255'],
            'og_image'          => ['nullable', 'string', 'max:500'],
            'og_description'    => ['nullable', 'string'],
            'genres'            => ['nullable', 'array'],
            'genres.*'          => ['integer', 'exists:genres,id'],
            'countries'         => ['nullable', 'array'],
            'countries.*'       => ['integer', 'exists:countries,id'],
            'director'          => ['nullable', 'integer', 'exists:people,id'],
            'producer'          => ['nullable', 'integer', 'exists:people,id'],
            'actors'            => ['nullable', 'array'],
            'actors.*'          => ['integer', 'exists:people,id'],
        ]);

        DB::beginTransaction();

        $uploadedFiles = [];

        try {
            // Xử lý Slug
            $slug = empty($validated['slug']) 
                ? Str::slug($validated['title']) 
                : Str::slug($validated['slug']);

            $slug = empty($slug) ? 'movie' : $slug;

            $originalSlug = $slug;
            $counter = 1;

            while (Movie::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;

            // Xử lý trạng thái và save_type
            if ($request->input('save_type') === 'draft') {
                $validated['status'] = 'draft';
            } elseif ($request->input('save_type') === 'publish') {
                $validated['status'] = 'ongoing';
            }

            $validated['is_published'] = $validated['status'] !== 'draft';

            // Xử lý Checkbox boolean
            $validated['featured']    = $request->boolean('featured');
            $validated['popular']     = $request->boolean('popular');
            $validated['recommended'] = $request->boolean('recommended');

            // Xử lý upload file media
            foreach (['poster' => 'posters', 'thumbnail' => 'thumbnails', 'backdrop' => 'backdrops'] as $field => $folder) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store("movies/{$folder}", 'public');
                    $uploadedFiles[] = $validated[$field];
                }
            }

            // Tách các trường quan hệ ra khỏi mảng dữ liệu chính của Movie
            $genres      = $validated['genres'] ?? [];
            $countries   = $validated['countries'] ?? [];
            $collections = $validated['collections'] ?? [];
            $tags       = $validated['tags'] ?? [];
            $director    = $validated['director'] ?? null;
            $producer    = $validated['producer'] ?? null;
            $actors      = $validated['actors'] ?? [];

            unset(
                $validated['genres'],
                $validated['countries'],
                $validated['collections'],
                $validated['tags'],
                $validated['director'],
                $validated['producer'],
                $validated['actors'],
                $validated['save_type']
            );

            // Tạo bản ghi Movie
            $movie = Movie::create($validated);

            // Đồng bộ quan hệ
            $movie->genres()->sync($genres);
            $movie->countries()->sync($countries);
            $movie->collections()->sync($collections);
            $movie->tags()->sync($tags);

            if ($director) {
                $movie->people()->attach($director, ['role' => 'director']);
            }

            if ($producer) {
                $movie->people()->attach($producer, ['role' => 'producer']);
            }

            if (!empty($actors)) {
                $actorData = collect($actors)->mapWithKeys(fn ($actorId) => [$actorId => ['role' => 'actor']])->toArray();
                $movie->people()->attach($actorData);
            }

            DB::commit();

            return redirect()
                ->route('admin.movies.index')
                ->with('success', 'Thêm phim thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $file) {
                Storage::disk('public')->delete($file);
            }

            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function edit($id)
    {
        $movie = Movie::with([
            'genres',
            'countries',
            'collections',
            'tags',
            'people',
        ])->findOrFail($id);

        // Danh sách dữ liệu cho các picker
        $countries = Country::orderBy('name')->get();

        $genres = Genre::orderBy('name')->get();

        $people = Person::orderBy('name')->get();

        $collections = Collection::where('is_active', true)
            ->orderBy('name')
            ->get();

        $tags = Tag::orderBy('name')->get();

        // Enum trong database
        $types = $this->getEnumValues('movies', 'type');

        $qualities = $this->getEnumValues('movies', 'quality');

        $languages = $this->getEnumValues('movies', 'language');

        $statuses = $this->getEnumValues('movies', 'status');


        /*
        |--------------------------------------------------------------------------
        | Selected Genres
        |--------------------------------------------------------------------------
        */

        $selectedGenres = $movie->genres
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Selected Countries
        |--------------------------------------------------------------------------
        */

        $selectedCountries = $movie->countries
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Selected Collections
        |--------------------------------------------------------------------------
        */

        $selectedCollections = $movie->collections
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Selected Tags
        |--------------------------------------------------------------------------
        */

        $selectedTags = $movie->tags
            ->pluck('id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Director
        |--------------------------------------------------------------------------
        */

        $director = $movie->people
            ->first(function ($person) {
                return $person->pivot->role === 'director';
            });


        /*
        |--------------------------------------------------------------------------
        | Producer
        |--------------------------------------------------------------------------
        */

        $producer = $movie->people
            ->first(function ($person) {
                return $person->pivot->role === 'producer';
            });


        /*
        |--------------------------------------------------------------------------
        | Actors
        |--------------------------------------------------------------------------
        */

        $actors = $movie->people
            ->filter(function ($person) {
                return $person->pivot->role === 'actor';
            })
            ->pluck('id')
            ->toArray();


        return view('admin.pages.movie.edit', compact(
            'movie',
            'countries',
            'genres',
            'people',
            'collections',
            'tags',
            'types',
            'qualities',
            'languages',
            'statuses',
            'selectedGenres',
            'selectedCountries',
            'selectedCollections',
            'selectedTags',
            'director',
            'producer',
            'actors'
        ));
    }


    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Movie information
            |--------------------------------------------------------------------------
            */

            'title'             => ['required', 'string', 'max:255'],

            'original_title'    => ['nullable', 'string', 'max:255'],

            'slug'              => ['nullable', 'string', 'max:255'],

            'type'              => ['required', 'in:single,series'],

            'release_year'      => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100'
            ],

            'release_date'      => ['nullable', 'date'],

            'duration'          => [
                'nullable',
                'integer',
                'min:0'
            ],

            'quality'           => [
                'required',
                'in:HD,Full HD,2K,4K'
            ],

            'language'          => [
                'required',
                'in:vietsub,thuyet_minh,long_tieng'
            ],

            'imdb_id'           => [
                'nullable',
                'string',
                'max:50'
            ],

            'imdb_rating'       => [
                'nullable',
                'numeric',
                'min:0',
                'max:10'
            ],

            'tmdb_id'           => [
                'nullable',
                'integer'
            ],


            /*
            |--------------------------------------------------------------------------
            | Tags
            |--------------------------------------------------------------------------
            */

            'tags'              => [
                'nullable',
                'array'
            ],

            'tags.*'            => [
                'integer',
                'exists:tags,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'short_description' => [
                'nullable',
                'string'
            ],

            'description'       => [
                'nullable',
                'string'
            ],

            'trailer_url'       => [
                'nullable',
                'url',
                'max:255'
            ],


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status'            => [
                'required',
                'in:draft,ongoing,completed'
            ],

            'save_type'         => [
                'required',
                'in:draft,update'
            ],


            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            'featured'          => [
                'nullable',
                'boolean'
            ],

            'popular'           => [
                'nullable',
                'boolean'
            ],

            'recommended'       => [
                'nullable',
                'boolean'
            ],


            /*
            |--------------------------------------------------------------------------
            | Media
            |--------------------------------------------------------------------------
            */

            'poster'            => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],

            'thumbnail'         => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ],

            'backdrop'          => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240'
            ],


            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'seo_title'         => [
                'nullable',
                'string',
                'max:255'
            ],

            'canonical_url'     => [
                'nullable',
                'url',
                'max:500'
            ],

            'seo_description'   => [
                'nullable',
                'string'
            ],

            'seo_keywords'      => [
                'nullable',
                'string'
            ],

            'og_title'          => [
                'nullable',
                'string',
                'max:255'
            ],

            'og_image'          => [
                'nullable',
                'string',
                'max:500'
            ],

            'og_description'    => [
                'nullable',
                'string'
            ],


            /*
            |--------------------------------------------------------------------------
            | Genres
            |--------------------------------------------------------------------------
            */

            'genres'            => [
                'nullable',
                'array'
            ],

            'genres.*'          => [
                'integer',
                'exists:genres,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | Countries
            |--------------------------------------------------------------------------
            */

            'countries'         => [
                'nullable',
                'array'
            ],

            'countries.*'       => [
                'integer',
                'exists:countries,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | Collections
            |--------------------------------------------------------------------------
            */

            'collections'       => [
                'nullable',
                'array'
            ],

            'collections.*'     => [
                'integer',
                'exists:collections,id'
            ],


            /*
            |--------------------------------------------------------------------------
            | People
            |--------------------------------------------------------------------------
            */

            'director'          => [
                'nullable',
                'integer',
                'exists:people,id'
            ],

            'producer'          => [
                'nullable',
                'integer',
                'exists:people,id'
            ],

            'actors'            => [
                'nullable',
                'array'
            ],

            'actors.*'          => [
                'integer',
                'exists:people,id'
            ],
        ]);


        DB::beginTransaction();

        $newFiles = [];

        $oldFiles = [];


        try {

            /*
            |--------------------------------------------------------------------------
            | Slug
            |--------------------------------------------------------------------------
            */

            $slug = empty($validated['slug'])
                ? Str::slug($validated['title'])
                : Str::slug($validated['slug']);

            $slug = empty($slug)
                ? 'movie'
                : $slug;

            $originalSlug = $slug;

            $counter = 1;

            while (
                Movie::where('slug', $slug)
                    ->where('id', '!=', $movie->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $counter;

                $counter++;
            }

            $validated['slug'] = $slug;


            /*
            |--------------------------------------------------------------------------
            | Status + Save type
            |--------------------------------------------------------------------------
            */

            if ($request->input('save_type') === 'draft') {

                $validated['status'] = 'draft';

                $validated['is_published'] = false;

            } else {

                $validated['is_published'] =
                    $validated['status'] !== 'draft';
            }


            /*
            |--------------------------------------------------------------------------
            | Checkbox
            |--------------------------------------------------------------------------
            */

            $featured = $request->boolean('featured');

            $popular = $request->boolean('popular');

            $recommended = $request->boolean('recommended');

            $validated['featured'] = $featured;

            $validated['is_featured'] = $featured;

            $validated['popular'] = $popular;

            $validated['recommended'] = $recommended;


            /*
            |--------------------------------------------------------------------------
            | Published At
            |--------------------------------------------------------------------------
            */

            $validated['published_at'] = $validated['is_published']
                ? ($movie->published_at ?? now())
                : null;


            /*
            |--------------------------------------------------------------------------
            | Upload media
            |--------------------------------------------------------------------------
            */

            $mediaFields = [
                'poster'    => 'movies/posters',
                'thumbnail' => 'movies/thumbnails',
                'backdrop'  => 'movies/backdrops',
            ];


            foreach ($mediaFields as $field => $folder) {

                if ($request->hasFile($field)) {

                    // Lưu đường dẫn file cũ
                    $oldFiles[$field] = $movie->$field;

                    // Upload file mới
                    $newPath = $request
                        ->file($field)
                        ->store($folder, 'public');

                    $validated[$field] = $newPath;

                    $newFiles[] = $newPath;

                } else {

                    // Không upload mới
                    // => giữ nguyên file cũ
                    unset($validated[$field]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Extract relationships
            |--------------------------------------------------------------------------
            */

            $genres = $validated['genres'] ?? [];

            $countries = $validated['countries'] ?? [];

            $collections = $validated['collections'] ?? [];

            $tags = $validated['tags'] ?? [];

            $director = $validated['director'] ?? null;

            $producer = $validated['producer'] ?? null;

            $actors = $validated['actors'] ?? [];


            /*
            |--------------------------------------------------------------------------
            | Remove relationship fields from Movie data
            |--------------------------------------------------------------------------
            */

            unset(
                $validated['genres'],
                $validated['countries'],
                $validated['collections'],
                $validated['tags'],
                $validated['director'],
                $validated['producer'],
                $validated['actors'],
                $validated['save_type']
            );


            /*
            |--------------------------------------------------------------------------
            | Update Movie
            |--------------------------------------------------------------------------
            */

            $movie->update($validated);


            /*
            |--------------------------------------------------------------------------
            | Sync Genres
            |--------------------------------------------------------------------------
            */

            $movie->genres()->sync($genres);


            /*
            |--------------------------------------------------------------------------
            | Sync Countries
            |--------------------------------------------------------------------------
            */

            $movie->countries()->sync($countries);


            /*
            |--------------------------------------------------------------------------
            | Sync Collections
            |--------------------------------------------------------------------------
            */

            $movie->collections()->sync($collections);


            /*
            |--------------------------------------------------------------------------
            | Sync Tags
            |--------------------------------------------------------------------------
            */

            $movie->tags()->sync($tags);


            /*
            |--------------------------------------------------------------------------
            | Sync People
            |--------------------------------------------------------------------------
            */

            $peopleData = [];


            // Director
            if ($director) {

                $peopleData[$director] = [
                    'role' => 'director'
                ];
            }


            // Producer
            if ($producer) {

                $peopleData[$producer] = [
                    'role' => 'producer'
                ];
            }


            // Actors
            foreach ($actors as $actorId) {

                $peopleData[$actorId] = [
                    'role' => 'actor'
                ];
            }


            $movie->people()->sync($peopleData);


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Delete old media
            |--------------------------------------------------------------------------
            */

            foreach ($oldFiles as $field => $oldPath) {

                if (
                    $oldPath &&
                    isset($validated[$field]) &&
                    $oldPath !== $validated[$field]
                ) {

                    Storage::disk('public')->delete($oldPath);
                }
            }


            return redirect()
                ->route('admin.movies.index')
                ->with(
                    'success',
                    'Cập nhật phim thành công!'
                );


        } catch (\Throwable $e) {

            DB::rollBack();


            /*
            |--------------------------------------------------------------------------
            | Delete newly uploaded files
            |--------------------------------------------------------------------------
            */

            foreach ($newFiles as $file) {

                Storage::disk('public')->delete($file);
            }


            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    public function featured()
    {
        $movies = Movie::query()
            ->where('featured', true)
            ->where('is_published', true)
            ->with(['genres'])
            ->withCount(['episodes', 'seasons'])
            ->orderByDesc('created_at')
            ->get();
        return view('admin.pages.movie.featured',compact('movies'));
    }
    public function unfeatured(Movie $movie)
    {
        $movie->update([
            'featured' => false,
        ]);

        return back()->with(
            'success',
            'Đã hủy trạng thái phim nổi bật.'
        );
    }
    public function popular()
    {
        $movies = Movie::query()
            ->where('popular', true)
            ->where('is_published', true)
            ->withCount([
                'views',
                'favorites',
            ])
            ->orderByDesc('views_count')
            ->get();
        return view('admin.pages.movie.popular',compact('movies'));
    }
    public function unpopular(Movie $movie)
    {
        $movie->update([
            'popular' => false,
        ]);

        return back()->with(
            'success',
            'Đã hủy trạng thái phim nổi bật.'
        );
    }
}
