<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Validation\Rule;

class EpisodeController extends Controller
{
    public function episodes()
    {
         $episodes = Episode::with(['movie', 'season'])
            ->orderBy('movie_id')
            ->orderBy('season_id')
            ->orderBy('episode_number')
            ->get();
        return view('admin.pages.episode.episodes',compact('episodes'));
    }

    /**
     * Form thêm episode
     */
    public function create()
    {
        $movies = Movie::where('type', 'series')
            ->orderBy('title')
            ->get();

        $seasons = Season::with('movie')
            ->orderBy('movie_id')
            ->orderBy('season_number')
            ->get();

        return view(
            'admin.pages.episode.add_episode',
            compact('movies', 'seasons')
        );
    }

    /**
     * Lưu episode
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'season_id' => [
                'required',
                'integer',
                'exists:seasons,id',
            ],

            'episode_number' => [
                'required',
                'integer',
                'min:1',

                Rule::unique('episodes', 'episode_number')
                    ->where(function ($query) use ($request) {
                        return $query->where('season_id', $request->season_id);
                    }),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'release_date' => [
                'nullable',
                'date',
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'season_id.required' => 'Vui lòng chọn season.',
            'season_id.exists' => 'Season không tồn tại.',

            'episode_number.required' => 'Vui lòng nhập số tập.',
            'episode_number.min' => 'Số tập phải lớn hơn hoặc bằng 1.',
            'episode_number.unique' =>
                'Season này đã tồn tại tập số :input. Vui lòng chọn số tập khác.',

            'name.required' => 'Vui lòng nhập tên tập phim.',

            'duration.integer' => 'Thời lượng phải là số.',
            'duration.min' => 'Thời lượng phải lớn hơn 0.',

            'release_date.date' => 'Ngày phát hành không hợp lệ.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_vip'] = $request->boolean('is_vip');
        $validated['is_new'] = $request->boolean('is_new');

        Episode::create($validated);

        return redirect()
            ->route('admin.episodes')
            ->with('success', 'Thêm tập phim thành công.');
    }

    /**
     * Form sửa episode
     */
    public function edit($id)
    {
        $episode = Episode::findOrFail($id);

        $movies = Movie::where('type', 'series')
            ->orderBy('title')
            ->get();

        $seasons = Season::with('movie')
            ->orderBy('movie_id')
            ->orderBy('season_number')
            ->get();

        return view(
            'admin.pages.episode.edit_episode',
            compact('episode', 'movies', 'seasons')
        );
    }

    /**
     * Cập nhật episode
     */
    public function update(Request $request, $id)
    {
        $episode = Episode::findOrFail($id);

        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'season_id' => [
                'required',
                'integer',
                'exists:seasons,id',
            ],

            'episode_number' => [
                'required',
                'integer',
                'min:1',

                Rule::unique('episodes', 'episode_number')
                    ->where(function ($query) use ($request) {
                        return $query->where('season_id', $request->season_id);
                    })
                    ->ignore($episode->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'release_date' => [
                'nullable',
                'date',
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'season_id.required' => 'Vui lòng chọn season.',
            'season_id.exists' => 'Season không tồn tại.',

            'episode_number.required' => 'Vui lòng nhập số tập.',
            'episode_number.min' => 'Số tập phải lớn hơn hoặc bằng 1.',
            'episode_number.unique' =>
                'Season này đã tồn tại tập số :input. Vui lòng chọn số tập khác.',

            'name.required' => 'Vui lòng nhập tên tập phim.',

            'duration.integer' => 'Thời lượng phải là số.',
            'duration.min' => 'Thời lượng phải lớn hơn 0.',

            'release_date.date' => 'Ngày phát hành không hợp lệ.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_vip'] = $request->boolean('is_vip');
        $validated['is_new'] = $request->boolean('is_new');

        $episode->update($validated);

        return redirect()
            ->route('admin.episodes')
            ->with('success', 'Cập nhật tập phim thành công.');
    }

    /**
     * Xóa episode
     */
    public function destroy($id)
    {
        $episode = Episode::findOrFail($id);

        $episode->delete();

        return redirect()
            ->route('admin.episodes')
            ->with('success', 'Xóa tập phim thành công.');
    }
}
