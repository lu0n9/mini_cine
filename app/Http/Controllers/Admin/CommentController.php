<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;


class CommentController extends Controller
{
    /**
     * Danh sách bình luận
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Comment::query()
            ->with([
                'user:id,name,email',
                'movie:id,title,slug',
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (in_array($filter, [
            'pending',
            'approved',
            'ai_review',
            'spam',
            'hidden',
        ])) {
            $query->where('status', $filter);
        } else {
            $filter = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | COMMENTS
        |--------------------------------------------------------------------------
        */

        $comments = $query
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | COUNTS
        |--------------------------------------------------------------------------
        */

        $counts = [
            'all' => Comment::count(),

            'pending' => Comment::where(
                'status',
                'pending'
            )->count(),

            'ai_review' => Comment::where(
                'status',
                'ai_review'
            )->count(),

            'spam' => Comment::where(
                'status',
                'spam'
            )->count(),

            'hidden' => Comment::where(
                'status',
                'hidden'
            )->count(),

            'approved' => Comment::where(
                'status',
                'approved'
            )->count(),
        ];

        return view(
            'admin.pages.comments.index',
            compact(
                'comments',
                'counts',
                'filter'
            )
        );
    }


    /**
     * Duyệt bình luận
     */
    public function approve(Comment $comment)
    {
        $comment->update([
            'status' => 'approved',
            'is_approved' => true,
        ]);

        return back()->with(
            'success',
            'Đã duyệt bình luận.'
        );
    }


    /**
     * Đánh dấu spam
     */
    public function spam(Comment $comment)
    {
        $comment->update([
            'status' => 'spam',
            'is_approved' => false,
        ]);

        return back()->with(
            'success',
            'Đã đánh dấu bình luận là spam.'
        );
    }


    /**
     * Ẩn bình luận
     */
    public function hide(Comment $comment)
    {
        $comment->update([
            'status' => 'hidden',
            'is_approved' => false,
        ]);

        return back()->with(
            'success',
            'Đã ẩn bình luận.'
        );
    }


    /**
     * Hiển thị lại bình luận
     */
    public function show(Comment $comment)
    {
        $comment->update([
            'status' => 'approved',
            'is_approved' => true,
        ]);

        return back()->with(
            'success',
            'Đã hiển thị lại bình luận.'
        );
    }


    /**
     * Xóa mềm
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with(
            'success',
            'Đã xóa bình luận.'
        );
    }
}