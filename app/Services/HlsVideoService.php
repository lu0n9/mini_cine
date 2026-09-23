<?php

namespace App\Services;

use App\Models\VideoProcessingJob;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class HlsVideoService
{
    /**
     * Chuyển video MP4 thành HLS.
     *
     * Output:
     *
     * storage/app/video-processing/hls/{job_id}/
     *
     * Bao gồm:
     * - master.m3u8
     * - 720p.m3u8
     * - 1080p.m3u8
     * - các file .ts
     */
    public function process(VideoProcessingJob $job): string
    {
        if (!$job->original_path) {
            throw new RuntimeException(
                'ProcessingJob không có original_path.'
            );
        }

        $inputPath = Storage::disk('local')->path($job->original_path);

        if (!File::exists($inputPath)) {
            throw new RuntimeException(
                "Không tìm thấy video gốc: {$inputPath}"
            );
        }

        $outputDirectory = storage_path(
            'app/video-processing/hls/' . $job->id
        );

        /*
         * Xóa output cũ nếu tồn tại.
         */
        if (File::exists($outputDirectory)) {
            File::deleteDirectory($outputDirectory);
        }

        File::makeDirectory(
            $outputDirectory,
            0755,
            true
        );

        /*
         * FFmpeg filter:
         *
         * Video gốc
         *      │
         *      ├── 720p
         *      │
         *      └── 1080p
         */
        $filter = implode(';', [
            '[0:v]split=2[v720][v1080]',
            '[v720]scale=-2:720[v720out]',
            '[v1080]scale=-2:1080[v1080out]',
        ]);

        $command = [
            'ffmpeg',

            '-y',

            '-i',
            $inputPath,

            '-filter_complex',
            $filter,

            /*
             * =========================
             * 720P
             * =========================
             */

            '-map',
            '[v720out]',

            '-map',
            '0:a?',

            '-c:v:0',
            'libx264',

            '-preset',
            'veryfast',

            '-crf',
            '23',

            '-c:a:0',
            'aac',

            '-b:a:0',
            '128k',

            /*
             * =========================
             * 1080P
             * =========================
             */

            '-map',
            '[v1080out]',

            '-map',
            '0:a?',

            '-c:v:1',
            'libx264',

            '-preset',
            'veryfast',

            '-crf',
            '23',

            '-c:a:1',
            'aac',

            '-b:a:1',
            '192k',

            /*
             * =========================
             * HLS
             * =========================
             */

            '-f',
            'hls',

            '-hls_time',
            '6',

            '-hls_playlist_type',
            'vod',

            '-hls_flags',
            'independent_segments',

            '-master_pl_name',
            'master.m3u8',

            '-var_stream_map',
            'v:0,a:0,name:720p v:1,a:1,name:1080p',

            '-hls_segment_filename',
            $outputDirectory . '/%v_%03d.ts',

            $outputDirectory . '/%v.m3u8',
        ];

        /*
         * Chạy FFmpeg.
         */
        $process = new Process($command);

        /*
         * Cho phép video dài chạy tối đa 24 giờ.
         */
        $process->setTimeout(86400);

        /*
         * Cập nhật trạng thái đang xử lý.
         */
        $job->update([
            'status' => 'processing',
            'progress' => 10,
            'started_at' => now(),
            'error_message' => null,
        ]);

        $process->run();

        /*
         * FFmpeg thất bại.
         */
        if (!$process->isSuccessful()) {
            $error = trim(
                $process->getErrorOutput()
            );

            $job->update([
                'status' => 'failed',
                'error_message' => $error,
            ]);

            throw new RuntimeException(
                "FFmpeg xử lý HLS thất bại:\n" . $error
            );
        }

        /*
         * Kiểm tra master playlist.
         */
        $masterPath = $outputDirectory . '/master.m3u8';

        if (!File::exists($masterPath)) {
            $message =
                'FFmpeg đã chạy nhưng không tạo được master.m3u8.';

            $job->update([
                'status' => 'failed',
                'error_message' => $message,
            ]);

            throw new RuntimeException($message);
        }

        /*
         * FFmpeg hoàn thành.
         */
        $job->update([
            'progress' => 80,
        ]);

        return $outputDirectory;
    }
}