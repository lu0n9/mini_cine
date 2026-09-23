<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CollectionController extends Controller
{
    /**
     * Danh sách Collection
     */
    public function index()
    {
        $collections = Collection::query()
            ->withCount('movies')
            ->with([
                'movies' => function ($query) {
                    $query->withCount('views');
                }
            ])
            ->orderBy('name')
            ->get();

        foreach ($collections as $collection) {
            $collection->total_views = $collection->movies->sum('views_count');
        }

        return view(
            'admin.pages.collections.index',
            compact('collections')
        );
    }

    /**
     * Form thêm Collection
     */
    public function create()
    {
        return view('admin.pages.collections.create');
    }

    /**
     * Thêm Collection
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:collections,name',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:collections,slug',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'poster' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'backdrop' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Collection.',
            'name.unique' => 'Tên Collection đã tồn tại.',

            'slug.unique' => 'Slug Collection đã tồn tại.',

            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',

            'poster.image' => 'Poster phải là file hình ảnh.',
            'poster.mimes' => 'Poster chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'poster.max' => 'Poster không được vượt quá 5MB.',

            'backdrop.image' => 'Backdrop phải là file hình ảnh.',
            'backdrop.mimes' => 'Backdrop chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'backdrop.max' => 'Backdrop không được vượt quá 10MB.',
        ]);

        // Tạo slug
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Kiểm tra slug sau khi xử lý
        if (Collection::where('slug', $slug)->exists()) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Collection đã tồn tại.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Poster
        |--------------------------------------------------------------------------
        */

        $posterPath = null;

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')
                ->store('collections', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Backdrop
        |--------------------------------------------------------------------------
        */

        $backdropPath = null;

        if ($request->hasFile('backdrop')) {
            $backdropPath = $request->file('backdrop')
                ->store('collections', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Collection
        |--------------------------------------------------------------------------
        */

        Collection::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'poster' => $posterPath,
            'backdrop' => $backdropPath,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.collections')
            ->with('success', 'Đã thêm Collection thành công.');
    }

    /**
     * Form sửa Collection
     */
    public function edit(Collection $collection)
    {
        return view(
            'admin.pages.collections.edit',
            compact('collection')
        );
    }

    /**
     * Cập nhật Collection
     */
    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('collections', 'name')
                    ->ignore($collection->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('collections', 'slug')
                    ->ignore($collection->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'poster' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'backdrop' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'remove_poster' => [
                'nullable',
                'boolean',
            ],

            'remove_backdrop' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Collection.',
            'name.unique' => 'Tên Collection đã tồn tại.',

            'slug.unique' => 'Slug Collection đã tồn tại.',

            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',

            'poster.image' => 'Poster phải là file hình ảnh.',
            'poster.mimes' => 'Poster chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'poster.max' => 'Poster không được vượt quá 5MB.',

            'backdrop.image' => 'Backdrop phải là file hình ảnh.',
            'backdrop.mimes' => 'Backdrop chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'backdrop.max' => 'Backdrop không được vượt quá 10MB.',
        ]);

        // Tạo slug
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Kiểm tra slug trùng Collection khác
        $exists = Collection::query()
            ->where('slug', $slug)
            ->where('id', '!=', $collection->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Collection đã tồn tại.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Dữ liệu cơ bản
        |--------------------------------------------------------------------------
        */

        $data = [
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Xóa Poster cũ
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('remove_poster') && $collection->poster) {

            Storage::disk('public')->delete($collection->poster);

            $data['poster'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Poster mới
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('poster')) {

            // Xóa ảnh cũ
            if ($collection->poster) {
                Storage::disk('public')->delete($collection->poster);
            }

            // Upload ảnh mới
            $data['poster'] = $request->file('poster')
                ->store('collections', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Xóa Backdrop cũ
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('remove_backdrop') && $collection->backdrop) {

            Storage::disk('public')->delete($collection->backdrop);

            $data['backdrop'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Backdrop mới
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('backdrop')) {

            // Xóa ảnh cũ
            if ($collection->backdrop) {
                Storage::disk('public')->delete($collection->backdrop);
            }

            // Upload ảnh mới
            $data['backdrop'] = $request->file('backdrop')
                ->store('collections', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $collection->update($data);

        return redirect()
            ->route('admin.collections')
            ->with('success', 'Đã cập nhật Collection thành công.');
    }

    /**
     * Xóa Collection
     */
    public function destroy(Collection $collection)
    {
        // Xóa Poster
        if ($collection->poster) {
            Storage::disk('public')->delete($collection->poster);
        }

        // Xóa Backdrop
        if ($collection->backdrop) {
            Storage::disk('public')->delete($collection->backdrop);
        }

        $collection->delete();

        return redirect()
            ->route('admin.collections')
            ->with('success', 'Đã xóa Collection thành công.');
    }

        /**
     * Xem chi tiết Collection
     */
    public function show(Collection $collection)
    {
        $collection->load([
            'movies' => function ($query) {
                $query
                    ->with(['genres'])
                    ->withCount(['views', 'episodes', 'seasons'])
                    ->orderBy('collection_movie.sort_order')
                    ->orderByDesc('movies.created_at');
            }
        ]);

        return view(
            'admin.pages.collections.show',
            compact('collection')
        );
    }


    /**
     * Xóa phim khỏi Collection
     */
    public function removeMovie(Collection $collection, $movie)
    {
        $collection->movies()->detach($movie);

        return back()->with(
            'success',
            'Đã xóa phim khỏi Collection.'
        );
    }
}