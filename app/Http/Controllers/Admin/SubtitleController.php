<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Subtitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubtitleController extends Controller
{
    /**
     * Danh sách subtitle
     */
    public function index(Request $request)
    {
        $query = Subtitle::with([
            'episode.movie',
            'episode.season',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($subtitleQuery) use ($search) {
                $subtitleQuery->where('language', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%")
                    ->orWhereHas('episode', function ($episodeQuery) use ($search) {
                        $episodeQuery->where('episode_number', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhereHas('movie', function ($movieQuery) use ($search) {
                                $movieQuery->where('title', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('movie_id')) {
            $query->whereHas('episode', fn ($episodeQuery) => $episodeQuery->where('movie_id', $request->input('movie_id')));
        }

        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        if ($request->input('active') === '1' || $request->input('active') === '0') {
            $query->where('is_active', $request->input('active') === '1');
        }

        if ($request->input('default') === '1' || $request->input('default') === '0') {
            $query->where('is_default', $request->input('default') === '1');
        }

        $subtitles = $query->orderBy('episode_id')
            ->orderBy('language')
            ->paginate(15)
            ->withQueryString();

        $movies = Movie::orderBy('title')->get(['id', 'title']);
        $languages = Subtitle::query()->whereNotNull('language')->distinct()->orderBy('language')->pluck('language');

        return view(
            'admin.pages.subtitle.subtitles',
            compact('subtitles', 'movies', 'languages')
        );
    }

    /**
     * Form thêm subtitle
     */
    public function create()
    {
        $movies = Movie::orderBy('title')->get();

        $episodes = Episode::with('movie')
            ->orderBy('movie_id')
            ->orderBy('episode_number')
            ->get();

        return view(
            'admin.pages.subtitle.add_subtitle',
            compact('movies', 'episodes')
        );
    }

    /**
     * Lưu subtitle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'episode_id' => [
                'required',
                'integer',
                'exists:episodes,id',
            ],

            'language' => [
                'required',
                'string',
                'max:10',
            ],

            'label' => [
                'required',
                'string',
                'max:100',
            ],

            'file_url' => [
                'required',
                'string',
                'max:500',
                'url',
            ],

            'format' => [
                'required',
                Rule::in(['srt', 'vtt']),
            ],
        ], [
            'episode_id.required' => 'Vui lòng chọn tập phim.',
            'episode_id.exists' => 'Tập phim không tồn tại.',

            'language.required' => 'Vui lòng nhập ngôn ngữ.',

            'label.required' => 'Vui lòng nhập label.',

            'file_url.required' => 'Vui lòng nhập link subtitle.',
            'file_url.url' => 'Link subtitle không hợp lệ.',
            'file_url.max' => 'Link subtitle không được vượt quá 500 ký tự.',

            'format.required' => 'Vui lòng chọn định dạng.',
            'format.in' => 'Định dạng subtitle không hợp lệ.',
        ]);

        // Kiểm tra không trùng language trong cùng episode
        $exists = Subtitle::where('episode_id', $validated['episode_id'])
            ->where('language', $validated['language'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'language' => 'Tập này đã có subtitle cho ngôn ngữ này.',
                ]);
        }

        $subtitle = Subtitle::create([
            'episode_id' => $validated['episode_id'],
            'language' => $validated['language'],
            'label' => $validated['label'],
            'file_url' => $validated['file_url'],
            'format' => $validated['format'],
            'is_default' => $request->boolean('is_default'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Nếu đặt mặc định
        if ($subtitle->is_default) {
            $this->setDefaultSubtitle(
                $subtitle->episode_id,
                $subtitle->id
            );
        }

        return redirect()
            ->route('admin.subtitles')
            ->with('success', 'Thêm subtitle thành công.');
    }

    /**
     * Form sửa subtitle
     */
    public function edit($id)
    {
        $subtitle = Subtitle::with([
            'episode.movie',
            'episode.season',
        ])->findOrFail($id);

        $movies = Movie::orderBy('title')->get();

        $episodes = Episode::with('movie')
            ->where('movie_id', $subtitle->episode->movie_id)
            ->orderBy('episode_number')
            ->get();

        return view(
            'admin.pages.subtitle.edit_subtitle',
            compact(
                'subtitle',
                'movies',
                'episodes'
            )
        );
    }

    /**
     * Cập nhật subtitle
     */
    public function update(Request $request, $id)
    {
        $subtitle = Subtitle::findOrFail($id);

        $validated = $request->validate([
            'episode_id' => [
                'required',
                'integer',
                'exists:episodes,id',
            ],

            'language' => [
                'required',
                'string',
                'max:10',
            ],

            'label' => [
                'required',
                'string',
                'max:100',
            ],

            'file_url' => [
                'required',
                'string',
                'max:500',
                'url',
            ],

            'format' => [
                'required',
                Rule::in(['srt', 'vtt']),
            ],
        ], [
            'episode_id.required' => 'Vui lòng chọn tập phim.',
            'episode_id.exists' => 'Tập phim không tồn tại.',

            'language.required' => 'Vui lòng nhập ngôn ngữ.',

            'label.required' => 'Vui lòng nhập label.',

            'file_url.required' => 'Vui lòng nhập link subtitle.',
            'file_url.url' => 'Link subtitle không hợp lệ.',
            'file_url.max' => 'Link subtitle không được vượt quá 500 ký tự.',

            'format.required' => 'Vui lòng chọn định dạng.',
            'format.in' => 'Định dạng subtitle không hợp lệ.',
        ]);

        // Không cho trùng episode + language
        $exists = Subtitle::where('episode_id', $validated['episode_id'])
            ->where('language', $validated['language'])
            ->where('id', '!=', $subtitle->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'language' => 'Tập này đã có subtitle cho ngôn ngữ này.',
                ]);
        }

        $subtitle->update([
            'episode_id' => $validated['episode_id'],
            'language' => $validated['language'],
            'label' => $validated['label'],
            'file_url' => $validated['file_url'],
            'format' => $validated['format'],
            'is_default' => $request->boolean('is_default'),
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($subtitle->is_default) {
            $this->setDefaultSubtitle(
                $subtitle->episode_id,
                $subtitle->id
            );
        }

        return redirect()
            ->route('admin.subtitles')
            ->with('success', 'Cập nhật subtitle thành công.');
    }

    /**
     * Xóa subtitle
     */
    public function destroy($id)
    {
        $subtitle = Subtitle::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Xóa file vật lý
        |--------------------------------------------------------------------------
        */
        if (
            $subtitle->file_url &&
            Storage::disk('public')->exists($subtitle->file_url)
        ) {
            Storage::disk('public')->delete($subtitle->file_url);
        }

        $subtitle->delete();

        return redirect()
            ->route('admin.subtitles')
            ->with('success', 'Xóa subtitle thành công.');
    }

    /**
     * Bật / tắt subtitle
     */
    public function toggleStatus($id)
    {
        $subtitle = Subtitle::findOrFail($id);

        $subtitle->update([
            'is_active' => !$subtitle->is_active,
        ]);

        return back()->with(
            'success',
            $subtitle->is_active
                ? 'Đã bật subtitle.'
                : 'Đã tắt subtitle.'
        );
    }

    /**
     * Đặt subtitle làm mặc định
     */
    public function setDefault($id)
    {
        $subtitle = Subtitle::findOrFail($id);

        $this->setDefaultSubtitle(
            $subtitle->episode_id,
            $subtitle->id
        );

        return back()->with(
            'success',
            'Đã đặt subtitle mặc định.'
        );
    }

    /**
     * Đặt duy nhất 1 subtitle mặc định trong episode
     */
    private function setDefaultSubtitle($episodeId, $subtitleId)
    {
        Subtitle::where('episode_id', $episodeId)
            ->update([
                'is_default' => false,
            ]);

        Subtitle::where('id', $subtitleId)
            ->update([
                'is_default' => true,
            ]);
    }

    /**
     * Lấy danh sách episode theo movie
     */
    public function getEpisodes($movieId)
    {
        $episodes = Episode::where('movie_id', $movieId)
            ->orderBy('season_id')
            ->orderBy('episode_number')
            ->get([
                'id',
                'movie_id',
                'season_id',
                'episode_number',
                'name',
            ]);

        return response()->json($episodes);
    }
}
