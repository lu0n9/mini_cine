<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => [
                'required',
                'exists:movies,id',
            ],

            'episode_id' => [
                'nullable',
                'exists:episodes,id',
            ],

            'type' => [
                'required',
            ],

            'message' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ], [
            'movie_id.required' => 'Không xác định được phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'episode_id.exists' => 'Tập phim không tồn tại.',

            'type.required' => 'Vui lòng chọn loại lỗi.',

            'message.required' => 'Vui lòng mô tả lỗi.',
            'message.min' => 'Mô tả lỗi phải có ít nhất 5 ký tự.',
            'message.max' => 'Mô tả lỗi không được vượt quá 2000 ký tự.',
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra episode có thuộc movie không
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['episode_id'])) {

            $episode = Episode::where('id', $validated['episode_id'])
                ->where('movie_id', $movie->id)
                ->first();

            if (!$episode) {

                return back()->withErrors([
                    'episode_id' => 'Tập phim không thuộc phim này.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tạo báo lỗi
        |--------------------------------------------------------------------------
        */

        Report::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
            'episode_id' => $validated['episode_id'] ?? null,
            'type' => $validated['type'],
            'message' => trim($validated['message']),
            'status' => 'pending',
            'admin_note' => null,
        ]);

        return back()->with(
            'report_success',
            'Cảm ơn bạn! Báo lỗi đã được gửi đến quản trị viên.'
        );
    }
}