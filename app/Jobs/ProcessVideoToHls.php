<?php

namespace App\Jobs;

use App\Models\MovieSource;
use App\Models\Subtitle;
use App\Models\VideoProcessingJob;
use App\Services\HlsVideoService;
use App\Services\SupabaseStorageService;
use App\Services\VideoProcessingCleanupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProcessVideoToHls implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ID của video_processing_jobs.
     */
    public int $processingJobId;

    /**
     * Danh sách subtitle đã upload tạm.
     */
    public array $subtitles;

    /**
     * Ngôn ngữ subtitle mặc định.
     */
    public ?string $subtitleDefaultLanguage;

    /**
     * ID của server API để upload (nullable = dùng mặc định).
     */
    public ?int $serverId;

    /**
     * Số lần thử.
     */
    public int $tries = 1;

    /**
     * Timeout 24 giờ.
     */
    public int $timeout = 86400;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $processingJobId,
        array $subtitles = [],
        ?string $subtitleDefaultLanguage = null,
        ?int $serverId = null
    ) {
        $this->processingJobId = $processingJobId;
        $this->subtitles = $subtitles;
        $this->subtitleDefaultLanguage = $subtitleDefaultLanguage;
        $this->serverId = $serverId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        HlsVideoService $hlsService,
        VideoProcessingCleanupService $cleanupService
    ): void {
        /*
         * ==========================================================
         * 1. LẤY PROCESSING JOB
         * ==========================================================
         */

        $job = VideoProcessingJob::with([
            'movie',
            'episode.season',
        ])->findOrFail(
            $this->processingJobId
        );

        // The selected API ID is serialized with this queued job.
        $supabase = new SupabaseStorageService($this->serverId);

        Log::info('ProcessVideoToHls started', [
            'processing_job_id' => $job->id,
            'movie_id' => $job->movie_id,
            'episode_id' => $job->episode_id,
            'server_id' => $this->serverId,
            'original_path' => $job->original_path,
        ]);

        /*
         * ==========================================================
         * 2. KIỂM TRA VIDEO GỐC
         * ==========================================================
         */

        if (!$job->original_path) {
            throw new RuntimeException(
                'ProcessingJob không có original_path.'
            );
        }

        $videoPath = Storage::disk('local')->path(
            $job->original_path
        );

        if (!File::exists($videoPath)) {
            throw new RuntimeException(
                "Không tìm thấy video gốc: {$videoPath}"
            );
        }

        /*
         * ==========================================================
         * 3. XỬ LÝ VIDEO -> HLS
         * ==========================================================
         */

        $job->update([
            'status' => 'processing',
            'progress' => 5,
            'started_at' => now(),
            'error_message' => null,
        ]);

        $outputDirectory = $hlsService->process($job);

        /*
         * ==========================================================
         * 4. THÔNG TIN MOVIE / EPISODE
         * ==========================================================
         */

        $movie = $job->movie;
        $episode = $job->episode;

        if (!$movie) {
            throw new RuntimeException(
                "Không tìm thấy movie ID {$job->movie_id}."
            );
        }

        if (!$episode) {
            throw new RuntimeException(
                "Không tìm thấy episode ID {$job->episode_id}."
            );
        }

        $movieSlug = $movie->slug ?: 'movie-' . $movie->id;

        $seasonNumber = 1;

        if ($episode->season) {
            $seasonNumber =
                $episode->season->season_number ?? 1;
        }

        $episodeNumber =
            $episode->episode_number ?? 1;

        /*
         * ==========================================================
         * 5. ĐƯỜNG DẪN SUPABASE
         * ==========================================================
         */

        $remoteBase =
            'hls/' .
            $movieSlug .
            '/season-' .
            $seasonNumber .
            '/episode-' .
            $episodeNumber .
            '/';

        /*
         * ==========================================================
         * 6. UPLOAD HLS FILES
         * ==========================================================
         */

        $files = File::allFiles(
            $outputDirectory
        );

        if (empty($files)) {
            throw new RuntimeException(
                'Không tìm thấy file HLS sau khi FFmpeg xử lý.'
            );
        }

        $masterRemotePath = null;

        foreach ($files as $file) {
            $relativePath = str_replace(
                $outputDirectory . DIRECTORY_SEPARATOR,
                '',
                $file->getPathname()
            );

            $relativePath = str_replace(
                DIRECTORY_SEPARATOR,
                '/',
                $relativePath
            );

            $remotePath =
                $remoteBase .
                $relativePath;

            $contentType = $this->contentType(
                $file->getExtension()
            );

            $supabase->upload(
                $remotePath,
                File::get($file->getPathname()),
                $contentType
            );

            if (
                $relativePath === 'master.m3u8'
            ) {
                $masterRemotePath = $remotePath;
            }
        }

        if (!$masterRemotePath) {
            throw new RuntimeException(
                'Không tìm thấy master.m3u8 trong output HLS.'
            );
        }

        /*
         * ==========================================================
         * 7. LƯU MOVIE SOURCES
         * ==========================================================
         *
         * Chỉ lưu playlist chất lượng:
         *
         * 720p
         * 1080p
         *
         * master.m3u8 là playlist tổng.
         */

        $this->saveMovieSources(
            $job,
            $outputDirectory,
            $remoteBase,
            $supabase
        );

        /*
         * ==========================================================
         * 8. UPLOAD SUBTITLE
         * ==========================================================
         */

        $this->uploadSubtitles(
            $job,
            $supabase,
            $remoteBase
        );

        /*
         * ==========================================================
         * 9. CẬP NHẬT HOÀN THÀNH
         * ==========================================================
         */

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'hls_path' => $masterRemotePath,
            'completed_at' => now(),
            'error_message' => null,
        ]);

        // Cloud uploads and source records are complete. Remove all local
        // video artifacts now; the status endpoint also retries this cleanup.
        $cleanupService->cleanupCompletedJob($job);

        /*
         * Xóa subtitle tạm.
         */
        $this->deleteTemporarySubtitles();

        Log::info('ProcessVideoToHls completed', [
            'processing_job_id' => $job->id,
            'hls_path' => $masterRemotePath,
        ]);
    }

    /**
     * Lưu MovieSource cho từng chất lượng.
     */
    protected function saveMovieSources(
        VideoProcessingJob $job,
        string $outputDirectory,
        string $remoteBase,
        SupabaseStorageService $supabase
    ): void {
        $qualities = [
            '720p',
            '1080p',
        ];

        foreach ($qualities as $quality) {
            $playlistPath =
                $outputDirectory .
                '/' .
                $quality .
                '.m3u8';

            if (!File::exists($playlistPath)) {
                continue;
            }

            $remotePath =
                $remoteBase .
                $quality .
                '.m3u8';

            $sourceUrl =
                $supabase->publicUrl(
                    $remotePath
                );

            MovieSource::updateOrCreate(
                [
                    'movie_id' => $job->movie_id,
                    'episode_id' => $job->episode_id,
                    'quality' => $quality,
                ],
                [
                    'server_name' => $supabase->serverName(),
                    'source_url' => $sourceUrl,
                    'type' => 'hls',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Upload subtitles lên Supabase.
     */
    protected function uploadSubtitles(
        VideoProcessingJob $job,
        SupabaseStorageService $supabase,
        string $remoteBase
    ): void {
        if (empty($this->subtitles)) {
            return;
        }

        foreach ($this->subtitles as $subtitle) {
            if (
                empty($subtitle['path']) ||
                empty($subtitle['language'])
            ) {
                continue;
            }

            $localPath = Storage::disk('local')->path(
                $subtitle['path']
            );

            if (!File::exists($localPath)) {
                Log::warning(
                    'Subtitle file not found',
                    [
                        'path' => $localPath,
                    ]
                );

                continue;
            }

            $language =
                strtolower(
                    trim(
                        $subtitle['language']
                    )
                );

            $format =
                strtolower(
                    $subtitle['format'] ?? 'vtt'
                );

            $remotePath =
                $remoteBase .
                'subtitles/' .
                $language .
                '.' .
                $format;

            $contentType =
                $this->subtitleContentType(
                    $format
                );

            $supabase->upload(
                $remotePath,
                File::get($localPath),
                $contentType
            );

            $subtitleUrl =
                $supabase->publicUrl(
                    $remotePath
                );

            Subtitle::updateOrCreate(
                [
                    'episode_id' => $job->episode_id,
                    'language' => $language,
                ],
                [
                    'label' =>
                        $subtitle['label']
                        ?? $this->languageLabel(
                            $language
                        ),

                    'file_url' => $subtitleUrl,

                    'format' => $format,

                    'is_default' =>
                        $language ===
                        $this->subtitleDefaultLanguage,

                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Xóa subtitle tạm.
     */
    protected function deleteTemporarySubtitles(): void
    {
        foreach ($this->subtitles as $subtitle) {
            if (empty($subtitle['path'])) {
                continue;
            }

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
    }

    /**
     * Content-Type của HLS files.
     */
    protected function contentType(
        string $extension
    ): string {
        return match (strtolower($extension)) {
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts' => 'video/mp2t',
            'mp4' => 'video/mp4',
            default => 'application/octet-stream',
        };
    }

    /**
     * Content-Type subtitle.
     */
    protected function subtitleContentType(
        string $format
    ): string {
        return match (strtolower($format)) {
            'vtt' => 'text/vtt',
            'srt' => 'application/x-subrip',
            default => 'text/plain',
        };
    }

    /**
     * Label ngôn ngữ.
     */
    protected function languageLabel(
        string $language
    ): string {
        return match ($language) {
            'vi' => 'Tiếng Việt',
            'en' => 'English',
            'zh' => '中文',
            'zh-cn' => '简体中文',
            'zh-tw' => '繁體中文',
            'ko' => '한국어',
            'ja' => '日本語',
            'th' => 'ไทย',
            'id' => 'Bahasa Indonesia',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'es' => 'Español',
            'pt' => 'Português',
            'ru' => 'Русский',
            default => strtoupper($language),
        };
    }

    /**
     * Queue failure handler.
     */
    public function failed(
        ?Throwable $exception
    ): void {
        try {
            $job = VideoProcessingJob::find(
                $this->processingJobId
            );

            if ($job) {
                $job->update([
                    'status' => 'failed',
                    'error_message' =>
                        $exception?->getMessage()
                        ?? 'Unknown error',
                ]);
            }

            Log::error(
                'ProcessVideoToHls failed',
                [
                    'processing_job_id' =>
                        $this->processingJobId,

                    'error' =>
                        $exception?->getMessage(),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Could not update failed processing job',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}
