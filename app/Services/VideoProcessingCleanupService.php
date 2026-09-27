<?php

namespace App\Services;

use App\Models\VideoProcessingJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VideoProcessingCleanupService
{
    /** Remove local source and generated HLS files once cloud processing succeeded. */
    public function cleanupCompletedJob(VideoProcessingJob $job): bool
    {
        if ($job->status !== 'completed') {
            return false;
        }

        $sourceRemoved = true;
        try {
            $disk = Storage::disk('local');
            $path = $job->original_path;

            if ($path && $disk->exists($path)) {
                $disk->delete($path);

                // Fall back to the resolved local path if the filesystem adapter
                // reports a failed delete without throwing an exception.
                if ($disk->exists($path)) {
                    File::delete($disk->path($path));
                }

                if ($disk->exists($path)) {
                    $sourceRemoved = false;
                    Log::warning('Could not remove completed video source from local storage', [
                        'processing_job_id' => $job->id,
                        'original_path' => $path,
                    ]);
                } else {
                    $directory = dirname($disk->path($path));
                    if (is_dir($directory) && count(array_diff(scandir($directory) ?: [], ['.', '..'])) === 0) {
                        @rmdir($directory);
                    }
                }
            }

            $hlsDirectory = storage_path('app/video-processing/hls/' . $job->id);
            if (File::isDirectory($hlsDirectory)) {
                File::deleteDirectory($hlsDirectory);
            }
        } catch (Throwable $exception) {
            $sourceRemoved = false;
            // The remote HLS upload remains complete; record cleanup failure
            // without incorrectly changing the processing job to failed.
            Log::error('Local video cleanup failed after cloud upload', [
                'processing_job_id' => $job->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return $sourceRemoved;
    }
}
