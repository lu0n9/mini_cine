<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Services\CommentModerationService;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => ['required', 'exists:movies,id'],
            'content' => ['required', 'string', 'min:2', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra movie
        |--------------------------------------------------------------------------
        */

        $movie = Movie::where('id', $validated['movie_id'])
            ->where('is_published', true)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Nếu là reply
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['parent_id'])) {

            $parentComment = Comment::where(
                    'id',
                    $validated['parent_id']
                )
                ->where('movie_id', $movie->id)
                ->first();

            if (!$parentComment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể trả lời bình luận này.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Không cho reply vào comment spam / hidden
            |--------------------------------------------------------------------------
            */

            if (in_array($parentComment->status, [
                'spam',
                'hidden',
            ])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể trả lời bình luận này.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Nội dung comment
        |--------------------------------------------------------------------------
        */

        $content = trim($validated['content']);

        /*
        |--------------------------------------------------------------------------
        | Moderation
        |--------------------------------------------------------------------------
        */

        $moderationService = app(
            \App\Services\CommentModerationService::class
        );

        $moderation = $moderationService->moderate(
            $content,
            auth()->id()
        );

        /*
        |--------------------------------------------------------------------------
        | Kết quả moderation
        |--------------------------------------------------------------------------
        |
        | approved  = hiển thị ngay
        | ai_review = vẫn hiển thị, Admin xem xét
        | spam      = không hiển thị
        |
        */

        $status = $moderation['decision'];

        /*
        |--------------------------------------------------------------------------
        | Tạo comment
        |--------------------------------------------------------------------------
        */

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $content,

            'is_approved' => in_array($status, [
                'approved',
                'ai_review',
            ]),

            'status' => $status,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Lưu kết quả moderation
        |--------------------------------------------------------------------------
        */

        $moderationService->saveResult(
            $comment,
            $moderation
        );

        /*
        |--------------------------------------------------------------------------
        | SPAM
        |--------------------------------------------------------------------------
        |
        | Comment vẫn được lưu DB để Admin xem.
        | Nhưng không trả comment cho frontend.
        |
        */

        if ($status === 'spam') {
            return response()->json([
                'success' => false,
                'moderated' => true,
                'status' => 'spam',
                'message' => 'Bình luận không hợp lệ.',
                'comment' => null,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Load user
        |--------------------------------------------------------------------------
        */

        $comment->load('user');

        /*
        |--------------------------------------------------------------------------
        | APPROVED / AI_REVIEW
        |--------------------------------------------------------------------------
        |
        | Cả hai đều được hiển thị.
        |
        */

        return response()->json([
            'success' => true,

            'moderated' => $status === 'ai_review',

            'status' => $status,

            'message' => !empty($validated['parent_id'])
                ? 'Đã trả lời bình luận.'
                : 'Đã đăng bình luận.',

            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'parent_id' => $comment->parent_id,

                'user_name' => $comment->user->game_name
                    ?? $comment->user->name,

                'created_at' => $comment->created_at
                    ->diffForHumans(),

                'likes_count' => 0,
            ],
        ]);
    }

    public function toggleLike($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if (!$comment->movie || !$comment->movie->is_published) {
            return response()->json([
                'message' => 'Không thể like bình luận này.'
            ], 404);
        }

        $like = CommentLike::where('user_id', auth()->id())
            ->where('comment_id', $comment->id)
            ->first();

        if ($like) {

            CommentLike::where('user_id', auth()->id())
                ->where('comment_id', $comment->id)
                ->delete();

            $liked = false;

        } else {

            CommentLike::create([
                'user_id' => auth()->id(),
                'comment_id' => $comment->id,
            ]);

            $liked = true;
        }

        $likesCount = CommentLike::where(
            'comment_id',
            $comment->id
        )->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }
}