<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Danh sách ratings
     */
    public function index(Request $request)
    {
        $query = Rating::query()
            ->with([
                'user:id,name,email',
                'movie:id,title,slug',
            ])
            ->latest('created_at');

        // Tìm kiếm user hoặc phim
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });

                $q->orWhereHas('movie', function ($movieQuery) use ($search) {
                    $movieQuery->where(
                        'title',
                        'like',
                        "%{$search}%"
                    );
                });

            });
        }

        // Lọc theo điểm
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $ratings = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.pages.ratings.index',
            compact('ratings')
        );
    }

    /**
     * Xóa rating
     */
    public function destroy(Rating $rating)
    {
        $rating->delete();

        return back()->with(
            'success',
            'Đã xóa đánh giá thành công.'
        );
    }
}