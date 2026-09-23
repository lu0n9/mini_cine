<?php

namespace App\Http\Controllers\client;

use App\Models\Favorite;
use App\Models\Movie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with([
                'movie.genres',
            ])
            ->latest()
            ->paginate(12);

        return view(
            'client.pages.favorite.index',
            compact('favorites')
        );
    }


    public function toggle(Request $request, Movie $movie)
    {
        if (!$movie->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Phim không tồn tại hoặc chưa được phát hành.',
            ], 404);
        }

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('movie_id', $movie->id)
            ->first();

        if ($favorite) {
            Favorite::where('user_id', auth()->id())
                ->where('movie_id', $movie->id)
                ->delete();

            return response()->json([
                'success' => true,
                'favorited' => false,
                'message' => 'Đã xóa khỏi danh sách của tôi.',
            ]);
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
        ]);

        return response()->json([
            'success' => true,
            'favorited' => true,
            'message' => 'Đã thêm vào danh sách của tôi.',
        ]);
    }
}