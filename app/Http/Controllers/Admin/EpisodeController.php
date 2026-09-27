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
    public function episodes(Request $request)
    {
        $query = Episode::with(['movie', 'season']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($episodeQuery) use ($search) {
                $episodeQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('episode_number', 'like', "%{$search}%")
                    ->orWhereHas('movie', function ($movieQuery) use ($search) {
                        $movieQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->input('movie_id'));
        }

        if ($request->filled('season_id')) {
            $query->where('season_id', $request->input('season_id'));
        }

        if ($request->input('published') === '1' || $request->input('published') === '0') {
            $query->where('is_published', $request->input('published') === '1');
        }

        $episodes = $query
            ->orderBy('movie_id')
            ->orderBy('season_id')
            ->orderBy('episode_number')
            ->paginate(15)
            ->withQueryString();

        $movies = Movie::orderBy('title')->get(['id', 'title']);
        $seasons = Season::with('movie')->orderBy('movie_id')->orderBy('season_number')->get();

        return view('admin.pages.episode.episodes', compact('episodes', 'movies', 'seasons'));
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
