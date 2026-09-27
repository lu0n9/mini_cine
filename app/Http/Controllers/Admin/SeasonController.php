<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Season;
use Illuminate\Validation\Rule;

class SeasonController extends Controller
{
    public function seasons(Request $request)
    {
        $query = Season::with('movie')->withCount('episodes');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($seasonQuery) use ($search) {
                $seasonQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('season_number', 'like', "%{$search}%")
                    ->orWhereHas('movie', function ($movieQuery) use ($search) {
                        $movieQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->input('movie_id'));
        }

        $seasons = $query->orderBy('movie_id')->orderBy('season_number')->paginate(15)->withQueryString();
        $movies = Movie::orderBy('title')->get(['id', 'title']);

        return view('admin.pages.seasons.seasons', compact('seasons', 'movies'));
    }

    public function createSeason(){
        $movies = Movie::orderBy('title')->get();
        return view('admin.pages.seasons.add_season',compact('movies'));
    }
    /**
     * Lưu season mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'season_number' => [
                'required',
                'integer',
                'min:1',

                Rule::unique('seasons', 'season_number')
                    ->where(function ($query) use ($request) {
                        return $query->where('movie_id', $request->movie_id);
                    }),
            ],

            'release_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',

            'movie_id.exists' => 'Phim không tồn tại.',

            'name.required' => 'Vui lòng nhập tên season.',

            'season_number.required' => 'Vui lòng nhập số thứ tự season.',

            'season_number.min' => 'Số season phải lớn hơn hoặc bằng 1.',

            'season_number.unique' =>
                'Phim này đã tồn tại season số :input. Vui lòng chọn số season khác.',

            'release_date.date' => 'Ngày phát hành không hợp lệ.',
        ]);

        // Checkbox
        $validated['is_active'] = $request->boolean('is_active');

        Season::create($validated);

        return redirect()
            ->route('admin.seasons')
            ->with('success', 'Thêm season thành công.');
    }
    public function edit($id)
    {
        $season = Season::with('movie')
            ->findOrFail($id);

        $movies = Movie::orderBy('title')->get();

        return view(
            'admin.pages.seasons.edit_season',
            compact(
                'season',
                'movies'
            )
        );
    }
     /**
     * Cập nhật season
     */
    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);

        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'season_number' => [
                'required',
                'integer',
                'min:1',

                Rule::unique('seasons', 'season_number')
                    ->where(function ($query) use ($request) {
                        return $query->where('movie_id', $request->movie_id);
                    })
                    ->ignore($season->id),
            ],

            'release_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'name.required' => 'Vui lòng nhập tên season.',

            'season_number.required' => 'Vui lòng nhập số thứ tự season.',
            'season_number.min' => 'Số season phải lớn hơn hoặc bằng 1.',

            'season_number.unique' =>
                'Phim này đã tồn tại season số :input. Vui lòng chọn số season khác.',

            'release_date.date' => 'Ngày phát hành không hợp lệ.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $season->update($validated);

        return redirect()
            ->route('admin.seasons')
            ->with('success', 'Cập nhật season thành công.');
    }
    /**
     * Xóa season
     */
    public function destroy($id)
    {
        $season = Season::findOrFail($id);

        $season->delete();


        return redirect()
            ->route('admin.seasons')
            ->with(
                'success',
                'Xóa season thành công.'
            );
    }
}
