<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Danh sách Tags
     */
    public function index(Request $request)
    {
        $query = Tag::query()
            ->withCount('movies');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        $tags = $query
            ->orderByDesc('movies_count')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.pages.tags.index',
            compact('tags')
        );
    }


    /**
     * Form thêm Tag
     */
    public function create()
    {
        return view('admin.pages.tags.create');
    }


    /**
     * Lưu Tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:tags,name',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:tags,slug',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Tag.',
            'name.unique' => 'Tag này đã tồn tại.',

            'slug.unique' => 'Slug Tag đã tồn tại.',

            'description.max' =>
                'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if (Tag::where('slug', $slug)->exists()) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Tag đã tồn tại.'
                ])
                ->withInput();
        }

        Tag::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.content.tags')
            ->with('success', 'Đã thêm Tag thành công.');
    }


    /**
     * Form sửa Tag
     */
    public function edit(Tag $tag)
    {
        return view(
            'admin.pages.tags.edit',
            compact('tag')
        );
    }


    /**
     * Cập nhật Tag
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')
                    ->ignore($tag->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')
                    ->ignore($tag->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Tag.',
            'name.unique' => 'Tag này đã tồn tại.',

            'slug.unique' => 'Slug Tag đã tồn tại.',

            'description.max' =>
                'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $exists = Tag::query()
            ->where('slug', $slug)
            ->where('id', '!=', $tag->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Tag đã tồn tại.'
                ])
                ->withInput();
        }

        $tag->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.tags')
            ->with('success', 'Đã cập nhật Tag thành công.');
    }


    /**
     * Xóa Tag
     */
    public function destroy(Tag $tag)
    {
        $moviesCount = $tag->movies()->count();

        if ($moviesCount > 0) {
            return back()->with(
                'error',
                "Không thể xóa Tag này vì đang được {$moviesCount} phim sử dụng."
            );
        }

        $tag->delete();

        return redirect()
            ->route('admin.content.tags')
            ->with('success', 'Đã xóa Tag thành công.');
    }
}