<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($newsQuery) use ($search) {
                $newsQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if (in_array($request->input('status'), ['draft', 'published'], true)) {
            $query->where('status', $request->input('status'));
        }

        $news = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.pages.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.pages.news.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?: $validated['title']);
        $validated['cover_image'] = $request->file('cover_image')?->store('news-covers', 'public');
        $validated['published_at'] = $this->publicationDate($validated);

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'Đã tạo tin tức.');
    }

    public function edit(News $news)
    {
        return view('admin.pages.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $this->validatedData($request, $news);
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?: $validated['title'], $news);
        $validated['published_at'] = $this->publicationDate($validated, $news);

        $previousCover = $news->cover_image;
        unset($validated['cover_image']);
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('news-covers', 'public');
        }

        $news->update($validated);

        if ($request->hasFile('cover_image') && $previousCover) {
            Storage::disk('public')->delete($previousCover);
        }

        return redirect()->route('admin.news.index')->with('success', 'Đã cập nhật tin tức.');
    }

    public function destroy(News $news)
    {
        if ($news->cover_image) {
            Storage::disk('public')->delete($news->cover_image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Đã xóa tin tức.');
    }

    private function validatedData(Request $request, ?News $news = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function uniqueSlug(string $value, ?News $news = null): string
    {
        $slug = Str::slug($value) ?: 'tin-tuc';
        $candidate = $slug;
        $suffix = 2;

        while (News::query()->where('slug', $candidate)->when($news, fn ($query) => $query->where('id', '!=', $news->id))->exists()) {
            $candidate = $slug . '-' . $suffix++;
        }

        return $candidate;
    }

    private function publicationDate(array $validated, ?News $news = null): mixed
    {
        if ($validated['status'] !== 'published') {
            return null;
        }

        return $validated['published_at'] ?? $news?->published_at ?? now();
    }
}
