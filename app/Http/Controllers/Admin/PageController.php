<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Danh sách trang
     */
    public function index()
    {
        $pages = Page::query()
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.pages.pages.index', compact('pages'));
    }


    /**
     * Form thêm trang
     */
    public function create()
    {
        return view('admin.pages.pages.create');
    }


    /**
     * Lưu trang mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:pages,slug',
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'published_at' => [
                'nullable',
                'date',
            ],

            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_description' => [
                'nullable',
                'string',
            ],

            'seo_keywords' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = $validated['slug']
            ?? Str::slug($validated['title']);

        $slug = $this->makeUniquePageSlug($slug);


        /*
        |--------------------------------------------------------------------------
        | Published At
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'published'
            && empty($validated['published_at'])
        ) {
            $validated['published_at'] = now();
        }


        $validated['slug'] = $slug;


        Page::create($validated);


        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Đã thêm trang thành công.');
    }


    /**
     * Form sửa trang
     */
    public function edit(Page $page)
    {
        return view(
            'admin.pages.pages.edit',
            compact('page')
        );
    }


    /**
     * Cập nhật trang
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:pages,slug,' . $page->id,
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],

            'published_at' => [
                'nullable',
                'date',
            ],

            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_description' => [
                'nullable',
                'string',
            ],

            'seo_keywords' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slug = $validated['slug']
            ?? Str::slug($validated['title']);

        $slug = $this->makeUniquePageSlug(
            $slug,
            $page->id
        );

        $validated['slug'] = $slug;


        /*
        |--------------------------------------------------------------------------
        | Published At
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'published'
            && empty($validated['published_at'])
        ) {
            $validated['published_at'] = $page->published_at ?? now();
        }


        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }


        $page->update($validated);


        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Đã cập nhật trang thành công.');
    }


    /**
     * Xóa trang
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Đã xóa trang.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ]);

        $file = $request->file('upload');

        $path = $file->store('pages', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function toggleStatus(Page $page)
    {
        if ($page->status === 'published') {

            $page->update([
                'status' => 'draft',
                'published_at' => null,
            ]);

            return back()->with(
                'success',
                'Đã chuyển trang về trạng thái Draft.'
            );
        }

        $page->update([
            'status' => 'published',
            'published_at' => $page->published_at ?? now(),
        ]);

        return back()->with(
            'success',
            'Đã xuất bản trang thành công.'
        );
    }

    /**
     * Tạo slug không trùng
     */
    protected function makeUniquePageSlug(
        string $slug,
        ?int $ignoreId = null
    ): string {
        $original = $slug;
        $counter = 1;


        while (
            Page::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }


        return $slug;
    }
}