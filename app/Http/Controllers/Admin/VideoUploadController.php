<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoToHls;
use App\Models\ApiKey;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\VideoProcessingJob;
use App\Services\VideoProcessingCleanupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class VideoUploadController extends Controller
{
    /**
     * Trang upload video.
     */
    public function create()
    {
        ApiKey::syncProjectApis();

        $movies = Movie::query()
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'slug',
            ]);

        $episodes = Episode::query()
            ->with([
                'movie:id,title',
                'season:id,movie_id,season_number',
            ])
            ->orderBy('movie_id')
            ->orderBy('season_id')
            ->orderBy('episode_number')
            ->get();

        $seasons = Season::query()
            ->with('movie:id,title')
            ->orderBy('movie_id')
            ->orderBy('season_number')
            ->get(['id', 'movie_id', 'season_number', 'name']);

        // Fetch active video storage servers for server selector
        $storageServers = ApiKey::where('type', 'video_storage')
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereNotNull('endpoint')
            ->where('endpoint', '!=', '')
            ->whereNotNull('key')
            ->where('key', '!=', '')
            ->get(['id', 'name', 'endpoint']);

        return view(
            'admin.pages.video.upload',
            compact(
                'movies',
                'episodes',
                'seasons',
                'storageServers'
            )
        );
    }

    /** Create a season from the upload page without leaving the form. */
    public function storeUploadSeason(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'season_number' => [
                'required', 'integer', 'min:1',
                Rule::unique('seasons', 'season_number')->where(fn ($query) => $query->where('movie_id', $request->input('movie_id'))),
            ],
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'season_number.unique' => 'Phim này đã có Season số :input.',
        ]);

        $season = Season::create([
            'movie_id' => $validated['movie_id'],
            'season_number' => $validated['season_number'],
            'name' => $validated['name'] ?: 'Season ' . $validated['season_number'],
        ]);

        return response()->json([
            'success' => true,
            'season' => [
                'id' => $season->id,
                'movie_id' => $season->movie_id,
                'season_number' => $season->season_number,
                'name' => $season->name,
                'label' => ($season->name ?: 'Season ' . $season->season_number) . ' · Season ' . $season->season_number,
            ],
        ], 201);
    }

    /** Create an empty episode ready for video upload. */
    public function storeUploadEpisode(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'season_id' => [
                'required', 'integer',
                Rule::exists('seasons', 'id')->where(fn ($query) => $query->where('movie_id', $request->input('movie_id'))),
            ],
            'episode_number' => [
                'required', 'integer', 'min:1',
                Rule::unique('episodes', 'episode_number')->where(fn ($query) => $query->where('movie_id', $request->input('movie_id'))->where('season_id', $request->input('season_id'))),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
        ], [
            'season_id.exists' => 'Season không thuộc Movie đang chọn.',
            'episode_number.unique' => 'Season này đã có tập số :input.',
        ]);

        $episode = new Episode([
            'movie_id' => $validated['movie_id'],
            'season_id' => $validated['season_id'],
            'episode_number' => $validated['episode_number'],
            'name' => $validated['name'] ?: 'Tập ' . $validated['episode_number'],
            'duration' => $validated['duration'] ?? null,
        ]);
        // Older installations may still have a required legacy video_url
        // column. Newer schemas store the actual stream on MovieSource.
        if (Schema::hasColumn('episodes', 'video_url')) {
            $episode->forceFill(['video_url' => '']);
        }
        $episode->save();
        $episode->load('season');

        return response()->json([
            'success' => true,
            'episode' => [
                'id' => $episode->id,
                'movie_id' => $episode->movie_id,
                'season_id' => $episode->season_id,
                'episode_number' => $episode->episode_number,
                'name' => $episode->name,
                'season_number' => $episode->season?->season_number ?? 1,
                'label' => 'S' . str_pad((string) ($episode->season?->season_number ?? 1), 2, '0', STR_PAD_LEFT)
                    . ' E' . str_pad((string) $episode->episode_number, 2, '0', STR_PAD_LEFT)
                    . ' — ' . $episode->name,
            ],
        ], 201);
    }


    /**
     * Upload video + subtitle.
     *
     * Flow:
     *
     * Browser
     * ↓
     * Controller
     * ↓
     * local temp
     * ↓
     * VideoProcessingJob
     * ↓
     * Queue
     */
    public function store(Request $request)
    {
        /*
         * =========================================================
         * VALIDATION
         * =========================================================
         */

        $validated = $request->validate(
            [
                'movie_id' => [
                    'required',
                    'integer',
                    'exists:movies,id',
                ],

                'episode_id' => [
                    'required',
                    'integer',
                    'exists:episodes,id',
                ],

                'video' => [
                    'required',
                    'file',
                    'mimetypes:video/mp4,video/quicktime,video/x-matroska,video/webm',
                    'max:5242880',
                ],

                'subtitles' => [
                    'nullable',
                    'array',
                    'max:10',
                ],

                'subtitles.*' => [
                    'file',
                    'mimes:vtt,srt',
                    'max:10240',
                ],

                'subtitle_default_language' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'server_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('api_keys', 'id')->where(function ($query) {
                        $query->where('type', 'video_storage')
                            ->where('status', 'active')
                            ->whereNotNull('endpoint')
                            ->where('endpoint', '!=', '')
                            ->whereNotNull('key')
                            ->where('key', '!=', '')
                            ->where(function ($expiry) {
                                $expiry->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                            });
                    }),
                ],
            ],
            [
                'movie_id.required' =>
                    'Vui lòng chọn phim.',

                'episode_id.required' =>
                    'Vui lòng chọn tập phim.',

                'server_id.exists' =>
                    'Server lưu trữ không hợp lệ hoặc đã tắt. Hãy chọn API Video Storage & CDN đang hoạt động.',

                'video.required' =>
                    'Vui lòng chọn video.',

                'video.max' =>
                    'Video không được vượt quá 5GB.',

                'subtitles.max' =>
                    'Chỉ được upload tối đa 10 subtitle.',

                'subtitles.*.mimes' =>
                    'Subtitle chỉ hỗ trợ VTT hoặc SRT.',

                'subtitles.*.max' =>
                    'Mỗi subtitle không được vượt quá 10MB.',
            ]
        );


        /*
         * =========================================================
         * KIỂM TRA EPISODE THUỘC MOVIE
         * =========================================================
         */

        $episode = Episode::with([
            'movie',
            'season',
        ])->findOrFail(
            $validated['episode_id']
        );


        if (
            (int) $episode->movie_id !==
            (int) $validated['movie_id']
        ) {

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Episode không thuộc movie đã chọn.',
                ],
                422
            );
        }


        if (!$episode->season) {

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Episode chưa thuộc season.',
                ],
                422
            );
        }


        /*
         * =========================================================
         * VIDEO
         * =========================================================
         */

        $videoFile =
            $request->file('video');

        if (!$videoFile) {

            return response()->json(
                [
                    'success' => false,
                    'message' =>
                        'Không nhận được file video.',
                ],
                422
            );
        }


        /*
         * =========================================================
         * SUBTITLE
         * =========================================================
         */

        $subtitleFiles =
            $request->file('subtitles', []);

        $subtitleDefaultLanguage =
            $request->input(
                'subtitle_default_language'
            );


        /*
         * =========================================================
         * VALIDATE SUBTITLE LANGUAGE
         * =========================================================
         */

        $subtitles = [];
        $languages = [];


        foreach (
            $subtitleFiles
            as $subtitleFile
        ) {

            $filename =
                strtolower(
                    $subtitleFile->getClientOriginalName()
                );


            /*
             * Loại bỏ .vtt / .srt
             */
            $withoutExtension =
                preg_replace(
                    '/\.(vtt|srt)$/i',
                    '',
                    $filename
                );


            /*
             * Lấy phần cuối:
             *
             * movie.vi.vtt
             *         ↓
             *        vi
             */
            $parts =
                explode(
                    '.',
                    $withoutExtension
                );


            $language =
                strtolower(
                    trim(
                        end($parts)
                    )
                );


            if (
                !preg_match(
                    '/^[a-z]{2,3}(?:-[a-z]{2,4})?$/',
                    $language
                )
            ) {

                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'Không xác định được ngôn ngữ subtitle từ file "' .
                            $subtitleFile->getClientOriginalName() .
                            '". Hãy đặt tên dạng vi.vtt, en.vtt, zh-cn.vtt...',
                    ],
                    422
                );
            }


            /*
             * Không cho trùng language
             * trong cùng request.
             */
            if (
                in_array(
                    $language,
                    $languages,
                    true
                )
            ) {

                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'Không được upload nhiều subtitle cùng ngôn ngữ: ' .
                            $language,
                    ],
                    422
                );
            }


            /*
             * Không cho trùng với DB.
             */
            $alreadyExists =
                \App\Models\Subtitle::query()
                    ->where(
                        'episode_id',
                        $episode->id
                    )
                    ->where(
                        'language',
                        $language
                    )
                    ->exists();


            if ($alreadyExists) {

                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'Episode đã có subtitle "' .
                            $language .
                            '". Hãy xóa subtitle cũ hoặc chọn ngôn ngữ khác.',
                    ],
                    422
                );
            }


            $languages[] =
                $language;


            $format =
                strtolower(
                    $subtitleFile->getClientOriginalExtension()
                );


            /*
             * Label.
             */
            $label =
                $this->languageLabel(
                    $language
                );


            /*
             * Lưu subtitle vào local temp.
             */
            $storedPath =
                $subtitleFile->store(
                    'video-processing/subtitles',
                    'local'
                );


            $subtitles[] = [
                'path' =>
                    $storedPath,

                'language' =>
                    $language,

                'label' =>
                    $label,

                'format' =>
                    $format,
            ];
        }


        /*
         * =========================================================
         * DEFAULT LANGUAGE
         * =========================================================
         */

        if (
            $subtitleDefaultLanguage
        ) {

            $subtitleDefaultLanguage =
                strtolower(
                    trim(
                        $subtitleDefaultLanguage
                    )
                );


            if (
                !in_array(
                    $subtitleDefaultLanguage,
                    $languages,
                    true
                )
            ) {

                /*
                 * Xóa subtitle temp
                 */
                foreach (
                    $subtitles
                    as $subtitle
                ) {

                    if (
                        Storage::disk('local')->exists(
                            $subtitle['path']
                        )
                    ) {
                        Storage::disk('local')->delete(
                            $subtitle['path']
                        );
                    }
                }


                return response()->json(
                    [
                        'success' => false,
                        'message' =>
                            'Subtitle mặc định không nằm trong danh sách subtitle đã upload.',
                    ],
                    422
                );
            }
        }


        /*
         * =========================================================
         * LƯU VIDEO TEMP
         * =========================================================
         */

        $videoPath = null;


        try {

            $videoPath =
                $videoFile->store(
                    'video-processing/originals',
                    'local'
                );


            if (!$videoPath) {
                throw new \RuntimeException(
                    'Không thể lưu video tạm.'
                );
            }


            /*
             * =====================================================
             * CREATE PROCESSING JOB
             * =====================================================
             */

            $VideoProcessingJob =
                VideoProcessingJob::create(
                    [
                        'movie_id' =>
                            $episode->movie_id,

                        'episode_id' =>
                            $episode->id,

                        'original_path' =>
                            $videoPath,

                        'status' =>
                            'pending',

                        'progress' =>
                            0,
                    ]
                );


            /*
             * =====================================================
             * DISPATCH QUEUE
             * =====================================================
             */

            ProcessVideoToHls::dispatch(
                $VideoProcessingJob->id,
                $subtitles,
                $subtitleDefaultLanguage,
                !empty($validated['server_id']) ? (int) $validated['server_id'] : null
            );


            Log::info(
                'Video upload queued',
                [
                    'processing_job_id' =>
                        $VideoProcessingJob->id,

                    'movie_id' =>
                        $episode->movie_id,

                    'episode_id' =>
                        $episode->id,

                    'server_id' =>
                        $validated['server_id'] ?? null,

                    'subtitle_count' =>
                        count($subtitles),
                ]
            );


            /*
             * =====================================================
             * JSON RESPONSE
             * =====================================================
             *
             * Đây là phần rất quan trọng.
             *
             * Blade đang dùng:
             *
             * JSON.parse(xhr.responseText)
             *
             * nên controller PHẢI trả JSON.
             */

            return response()->json(
                [
                    'success' =>
                        true,

                    'message' =>
                        'Upload thành công. Video đang được xử lý.',

                    'job_id' =>
                        $VideoProcessingJob->id,

                    'status' =>
                        'pending',

                    'progress' =>
                        0,
                ],
                201
            );

        } catch (Throwable $e) {

            /*
             * Xóa video temp nếu tạo job thất bại.
             */
            if (
                $videoPath &&
                Storage::disk('local')->exists(
                    $videoPath
                )
            ) {
                Storage::disk('local')->delete(
                    $videoPath
                );
            }


            /*
             * Xóa subtitle temp.
             */
            foreach (
                $subtitles
                as $subtitle
            ) {

                if (
                    !empty($subtitle['path']) &&
                    Storage::disk('local')->exists(
                        $subtitle['path']
                    )
                ) {
                    Storage::disk('local')->delete(
                        $subtitle['path']
                    );
                }
            }


            Log::error(
                'Video upload failed',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );


            /*
             * TUYỆT ĐỐI không dd()
             * ở AJAX endpoint.
             *
             * Luôn trả JSON.
             */
            return response()->json(
                [
                    'success' =>
                        false,

                    'message' =>
                        'Không thể tạo tiến trình xử lý video: ' .
                        $e->getMessage(),
                ],
                500
            );
        }
    }


    /**
     * API kiểm tra processing status.
     */
    public function status($id, VideoProcessingCleanupService $cleanupService)
    {
        $job =
            VideoProcessingJob::find($id);


        if (!$job) {

            return response()->json(
                [
                    'success' =>
                        false,

                    'status' =>
                        'failed',

                    'progress' =>
                        0,

                    'error' =>
                        'Không tìm thấy processing job.',
                ],
                404
            );
        }

        $localSourceRemoved = $job->status === 'completed'
            ? $cleanupService->cleanupCompletedJob($job)
            : null;


        return response()->json(
            [
                'success' =>
                    true,

                'job_id' =>
                    $job->id,

                'status' =>
                    $job->status,

                'progress' =>
                    (int) (
                        $job->progress ?? 0
                    ),

                'error' =>
                    $job->error_message,

                'local_source_removed' => $localSourceRemoved,
            ]
        );
    }


    /**
     * Label ngôn ngữ.
     */
    private function languageLabel(
        string $language
    ): string {

        return match ($language) {

            'vi' =>
                'Tiếng Việt',

            'en' =>
                'English',

            'zh' =>
                '中文',

            'zh-cn' =>
                '简体中文',

            'zh-tw' =>
                '繁體中文',

            'ko' =>
                '한국어',

            'ja' =>
                '日本語',

            'th' =>
                'ไทย',

            'id' =>
                'Bahasa Indonesia',

            'fr' =>
                'Français',

            'de' =>
                'Deutsch',

            'es' =>
                'Español',

            'pt' =>
                'Português',

            'ru' =>
                'Русский',

            default =>
                strtoupper($language),
        };
    }
}
