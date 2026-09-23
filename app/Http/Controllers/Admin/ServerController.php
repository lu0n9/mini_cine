<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\MovieSource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ServerController extends Controller
{
    /**
     * Danh sách tất cả server/source
     */
    public function index()
    {
        $sources = MovieSource::with(['movie', 'episode'])
            ->orderBy('movie_id')
            ->orderBy('episode_id')
            ->orderBy('server_name')
            ->get();

        return view('admin.pages.server.servers', compact('sources'));
    }

    
    private function getEnumValues($table, $column)
    {
        $columnInfo = DB::selectOne(
            "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'"
        );

        if (!$columnInfo || !isset($columnInfo->Type)) {
            return [];
        }

        preg_match("/^enum\((.*)\)$/", $columnInfo->Type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        return str_getcsv($matches[1], ',', "'");
    }
    /**
     * Form thêm server/source
     */
    public function create()
    {
        $movies = Movie::orderBy('title')->get();

        $episodes = Episode::with('movie')
            ->orderBy('movie_id')
            ->orderBy('episode_number')
            ->get();

        $qualities = $this->getEnumValues('movie_sources', 'quality');
        return view(
            'admin.pages.server.add_server',
            compact('movies', 'episodes','qualities')
        );
    }

    /**
     * Lưu server/source
     */
  public function store(Request $request)
    {
        // Lấy các giá trị ENUM trực tiếp từ database
        $qualities = $this->getEnumValues('movie_sources', 'quality');

        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'episode_id' => [
                'nullable',
                'integer',
                'exists:episodes,id',
            ],

            'server_name' => [
                'required',
                'string',
                'max:100',
            ],

            'source_url' => [
                'required',
                'string',
                'max:500',
                'url',
            ],

            'type' => [
                'required',
                Rule::in([
                    'hls',
                    'mp4',
                    'embed',
                    'dash',
                ]),
            ],

            'quality' => [
                'nullable',
                Rule::in($qualities),
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'episode_id.exists' => 'Episode không tồn tại.',

            'server_name.required' => 'Vui lòng nhập tên server.',

            'source_url.required' => 'Vui lòng nhập đường dẫn video.',
            'source_url.url' => 'Đường dẫn video không hợp lệ.',

            'type.required' => 'Vui lòng chọn loại nguồn phát.',
            'type.in' => 'Loại nguồn phát không hợp lệ.',

            'quality.in' => 'Chất lượng không hợp lệ.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra Episode có thuộc đúng Movie không
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['episode_id'])) {

            $episode = Episode::find($validated['episode_id']);

            if (
                !$episode ||
                $episode->movie_id != $validated['movie_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'episode_id' => 'Episode không thuộc phim đã chọn.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Trạng thái
        |--------------------------------------------------------------------------
        */

        $validated['is_active'] = $request->boolean('is_active');

        /*
        |--------------------------------------------------------------------------
        | Lưu database
        |--------------------------------------------------------------------------
        */

        MovieSource::create($validated);

        return redirect()
            ->route('admin.servers')
            ->with('success', 'Thêm server thành công.');
    }

    /**
     * Form sửa server/source
     */
   public function edit($id)
    {
        $source = MovieSource::findOrFail($id);

        $movies = Movie::orderBy('title')->get();

        $episodes = Episode::with('movie')
            ->where('movie_id', $source->movie_id)
            ->orderBy('episode_number')
            ->get();

        // Lấy ENUM từ database
        $types = $this->getEnumValues('movie_sources', 'type');

        $qualities = $this->getEnumValues('movie_sources', 'quality');

        return view(
            'admin.pages.server.edit_server',
            compact(
                'source',
                'movies',
                'episodes',
                'types',
                'qualities'
            )
        );
    }

    /**
     * Cập nhật server/source
     */
    public function update(Request $request, $id)
    {
        $types = $this->getEnumValues('movie_sources', 'type');
        $qualities = $this->getEnumValues('movie_sources', 'quality');

        $validated = $request->validate([
            'movie_id' => [
                'required',
                'integer',
                'exists:movies,id',
            ],

            'episode_id' => [
                'nullable',
                'integer',
                'exists:episodes,id',
            ],

            'server_name' => [
                'required',
                'string',
                'max:100',
            ],

            'source_url' => [
                'required',
                'string',
                'max:500',
                'url',
            ],

            'type' => [
                'required',
                Rule::in([
                    'hls',
                    'mp4',
                    'embed',
                    'dash',
                ]),
            ],

            'quality' => [
                'nullable',
                Rule::in($qualities),
            ],
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'movie_id.exists' => 'Phim không tồn tại.',

            'episode_id.exists' => 'Episode không tồn tại.',

            'server_name.required' => 'Vui lòng nhập tên server.',

            'source_url.required' => 'Vui lòng nhập đường dẫn video.',
            'source_url.url' => 'Đường dẫn video không hợp lệ.',

            'type.required' => 'Vui lòng chọn loại nguồn phát.',
            'type.in' => 'Loại nguồn phát không hợp lệ.',

            'quality.in' => 'Chất lượng không hợp lệ.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra Episode có thuộc đúng Movie không
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['episode_id'])) {
            $episode = Episode::find($validated['episode_id']);

            if (!$episode || $episode->movie_id != $validated['movie_id']) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'episode_id' => 'Episode không thuộc phim đã chọn.',
                    ]);
            }
        }

        $validated['is_active'] = $request->boolean('is_active');

        $source->update($validated);

        return redirect()
            ->route('admin.servers')
            ->with('success', 'Cập nhật server thành công.');
    }

    /**
     * Xóa server/source
     */
    public function destroy($id)
    {
        $source = MovieSource::findOrFail($id);

        $source->delete();

        return redirect()
            ->route('admin.servers')
            ->with('success', 'Xóa server thành công.');
    }

    /**
     * Bật / tắt server
     */
    public function toggleStatus($id)
    {
        $source = MovieSource::findOrFail($id);

        $source->update([
            'is_active' => !$source->is_active,
        ]);

        return back()->with(
            'success',
            $source->is_active
                ? 'Đã bật server.'
                : 'Đã tắt server.'
        );
    }

    /**
     * AJAX: lấy danh sách Episode theo Movie
     */
    public function getEpisodes($movieId)
    {
        $episodes = Episode::where('movie_id', $movieId)
            ->orderBy('episode_number')
            ->get([
                'id',
                'episode_number',
                'name',
            ]);

        return response()->json($episodes);
    }
}