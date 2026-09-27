<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Trang chính Forum
     */
    public function index()
    {
        $categories = ForumCategory::query()
            ->where('is_active', true)
            ->withCount([
                'posts' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        $posts = ForumPost::published()
            ->with([
                'user',
                'category',
            ])
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->paginate(10);

        return view('client.pages.forum.index', compact(
            'categories',
            'posts'
        ));
    }

    /**
     * Danh sách bài viết theo danh mục
     */
    public function category(string $slug)
    {
        $category = ForumCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = ForumPost::published()
            ->where('category_id', $category->id)
            ->with([
                'user',
                'category',
            ])
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->paginate(10);

        return view('client.pages.forum.category', compact(
            'category',
            'posts'
        ));
    }

    /**
     * Xem bài viết
     */
    public function show(string $slug)
    {
        $post = ForumPost::published()
            ->with([
                'user',
                'category',
                'comments' => function ($query) {
                    $query
                        ->whereNull('parent_id')
                        ->where('status', 'approved')
                        ->with([
                            'user',
                            'replies' => function ($replyQuery) {
                                $replyQuery
                                    ->where('status', 'approved')
                                    ->with('user')
                                    ->oldest();
                            },
                        ])
                        ->latest();
                },
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('views_count');

        return view('client.pages.forum.show', compact('post'));
    }

    /**
     * Form tạo bài
     */
    public function create()
    {
        $categories = ForumCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('client.pages.forum.create', compact('categories'));
    }

    /**
     * Lưu bài viết
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:forum_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
        ]);

        $category = ForumCategory::query()
            ->where('id', $validated['category_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $slug = Str::slug($validated['title']);

        $slug = $this->makeUniqueSlug(
            ForumPost::class,
            $slug
        );

        $post = ForumPost::create([
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'status' => 'published',
            'is_pinned' => false,
            'is_locked' => false,
            'published_at' => now(),
        ]);

        return redirect()
            ->route('forum.show', $post->slug)
            ->with('success', 'Đã đăng bài viết thành công.');
    }

    /**
     * Form sửa bài
     */
    public function edit(ForumPost $post)
    {
        abort_unless(
            $post->user_id === auth()->id(),
            403
        );

        $categories = ForumCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('client.pages.forum.edit', compact(
            'post',
            'categories'
        ));
    }

    /**
     * Cập nhật bài
     */
    public function update(
        Request $request,
        ForumPost $post
    ) {
        abort_unless(
            $post->user_id === auth()->id(),
            403
        );

        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:forum_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],
        ]);

        $slug = Str::slug($validated['title']);

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
        ]);

        return redirect()
            ->route('forum.show', $post->slug)
            ->with('success', 'Đã cập nhật bài viết.');
    }

    /**
     * Xóa bài
     */
    public function destroy(ForumPost $post)
    {
        abort_unless(
            $post->user_id === auth()->id(),
            403
        );

        $post->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Đã xóa bài viết.');
    }

    /**
     * Lưu bình luận
     */
    public function commentStore(Request $request, ForumPost $post)
    {
        // Bài phải đang được phép hiển thị
        abort_unless(
            $post->status === 'published',
            404
        );

        // Không cho bình luận bài đã khóa
        if ($post->is_locked) {
            return back()->with(
                'error',
                'Bài viết này đã được khóa và không nhận thêm bình luận.'
            );
        }

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:5000',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:forum_comments,id',
            ],
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'content.max' => 'Bình luận không được vượt quá 5000 ký tự.',
            'parent_id.exists' => 'Bình luận được trả lời không tồn tại.',
        ]);

        $parentId = null;
        if (!empty($validated['parent_id'])) {
            $parentComment = ForumComment::where('id', $validated['parent_id'])
                ->where('post_id', $post->id)
                ->where('status', 'approved')
                ->first();

            if (!$parentComment) {
                return back()->with(
                    'error',
                    'Bình luận bạn muốn trả lời không tồn tại hoặc không hợp lệ.'
                );
            }

            // Đảm bảo cấu trúc phân cấp: gom về cấp cha gốc nếu reply vào một câu trả lời
            $parentId = $parentComment->parent_id ?: $parentComment->id;
        }

        $comment = ForumComment::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'content' => $validated['content'],
            'status' => 'approved',
            'likes_count' => 0,
        ]);

        // Cập nhật số lượng bình luận
        $post->increment('comments_count');

        return redirect()
            ->to(route('forum.show', $post->slug) . '#comment-' . $comment->id)
            ->with(
                'success',
                $parentId ? 'Đã gửi câu trả lời thành công.' : 'Đã gửi bình luận thành công.'
            );
    }
    /**
     * Danh sách bài viết của tôi
     */
    public function myPosts()
    {
        $posts = ForumPost::query()
            ->where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'client.pages.forum.my-posts',
            compact('posts')
        );
    }
}