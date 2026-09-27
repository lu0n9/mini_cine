<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $stats = [
            'categories' => ForumCategory::count(),

            'posts' => ForumPost::count(),

            'published_posts' => ForumPost::where('status', 'published')->count(),

            'pending_posts' => ForumPost::where('status', 'draft')->count(),

            'comments' => ForumComment::count(),

            'pending_comments' => ForumComment::where('status', 'pending')->count(),
        ];

        $latestPosts = ForumPost::with([
                'user',
                'category',
            ])
            ->latest()
            ->take(10)
            ->get();

        $latestComments = ForumComment::with([
                'user',
                'post',
            ])
            ->latest()
            ->take(10)
            ->get();

        return view(
            'admin.pages.forum.index',
            compact(
                'stats',
                'latestPosts',
                'latestComments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    public function categories()
    {
        $categories = ForumCategory::query()
            ->withCount('posts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view(
            'admin.pages.forum.categories.index',
            compact('categories')
        );
    }

    public function createCategory()
    {
        return view('admin.pages.forum.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:50',
            ],

            'background_image' => [
                'nullable',
                'string',
                'max:500',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $slug = $validated['slug']
            ?: Str::slug($validated['name']);

        $slug = $this->makeUniqueSlug(
            ForumCategory::class,
            $slug
        );

        ForumCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'background_image' => $validated['background_image'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.forum.categories')
            ->with('success', 'Đã tạo danh mục forum.');
    }

    public function editCategory(ForumCategory $category)
    {
        return view(
            'admin.pages.forum.categories.edit',
            compact('category')
        );
    }

    public function updateCategory(
        Request $request,
        ForumCategory $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:50',
            ],

            'background_image' => [
                'nullable',
                'string',
                'max:500',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $slug = $validated['slug']
            ?: Str::slug($validated['name']);

        $slug = $this->makeUniqueSlug(
            ForumCategory::class,
            $slug,
            $category->id
        );

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'background_image' => $validated['background_image'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.forum.categories')
            ->with('success', 'Đã cập nhật danh mục.');
    }

    public function toggleCategory(ForumCategory $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return back()->with(
            'success',
            $category->is_active
                ? 'Đã bật danh mục.'
                : 'Đã tắt danh mục.'
        );
    }

    public function destroyCategory(ForumCategory $category)
    {
        if ($category->posts()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa danh mục đang có bài viết.'
            );
        }

        $category->delete();

        return back()->with(
            'success',
            'Đã xóa danh mục.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | POSTS
    |--------------------------------------------------------------------------
    */

    public function posts(Request $request)
    {
        $query = ForumPost::query()
            ->with([
                'user',
                'category',
            ]);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->input('category_id')
            );
        }

        $posts = $query
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = ForumCategory::query()
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.pages.forum.posts.index',
            compact(
                'posts',
                'categories'
            )
        );
    }

    public function editPost(ForumPost $post)
    {
        $categories = ForumCategory::query()
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.pages.forum.posts.edit',
            compact(
                'post',
                'categories'
            )
        );
    }

    public function updatePost(
        Request $request,
        ForumPost $post
    ) {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'exists:forum_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],

            'status' => [
                'required',
                'in:draft,published,hidden',
            ],
        ]);

        $slug = $validated['slug']
            ?: Str::slug($validated['title']);

        $slug = $this->makeUniqueSlug(
            ForumPost::class,
            $slug,
            $post->id
        );

        $post->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('admin.forum.posts')
            ->with('success', 'Đã cập nhật bài viết.');
    }

    public function togglePostPin(ForumPost $post)
    {
        $post->update([
            'is_pinned' => !$post->is_pinned,
        ]);

        return back()->with(
            'success',
            $post->is_pinned
                ? 'Đã ghim bài viết.'
                : 'Đã bỏ ghim bài viết.'
        );
    }

    public function togglePostLock(ForumPost $post)
    {
        $post->update([
            'is_locked' => !$post->is_locked,
        ]);

        return back()->with(
            'success',
            $post->is_locked
                ? 'Đã khóa bài viết.'
                : 'Đã mở khóa bài viết.'
        );
    }

    public function deletePost(ForumPost $post)
    {
        $post->delete();

        return back()->with(
            'success',
            'Đã xóa bài viết.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COMMENTS
    |--------------------------------------------------------------------------
    */

    public function comments(Request $request)
    {
        $query = ForumComment::query()
            ->with([
                'user',
                'post',
            ]);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');

            $query->where(
                'content',
                'like',
                "%{$keyword}%"
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        $comments = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.pages.forum.comments.index',
            compact('comments')
        );
    }

    public function updateCommentStatus(
        Request $request,
        ForumComment $comment
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:approved,pending,hidden',
            ],
        ]);

        $comment->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Đã cập nhật trạng thái bình luận.'
        );
    }

    public function deleteComment(
        ForumComment $comment
    ) {
        $comment->delete();

        return back()->with(
            'success',
            'Đã xóa bình luận.'
        );
    }
}