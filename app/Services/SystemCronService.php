<?php

namespace App\Services;

use App\Models\SystemCronRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class SystemCronService
{
    public static function jobs(): array
    {
        return [
            'database-backup' => ['name' => 'Backup database', 'schedule' => 'Hằng ngày 02:00'],
            'generate-sitemap' => ['name' => 'Generate sitemap', 'schedule' => 'Hằng ngày 03:00'],
            'clean-expired-sessions' => ['name' => 'Clean expired sessions', 'schedule' => 'Mỗi giờ'],
        ];
    }

    public function run(string $jobKey): SystemCronRun
    {
        abort_unless(array_key_exists($jobKey, self::jobs()), 404);
        $started = microtime(true);

        try {
            $message = match ($jobKey) {
                'database-backup' => $this->backupDatabase(),
                'generate-sitemap' => $this->generateSitemap(),
                'clean-expired-sessions' => $this->cleanExpiredSessions(),
            };
            $status = 'success';
        } catch (\Throwable $exception) {
            $status = 'failed';
            $message = $exception->getMessage();
            Log::error('Scheduled system task failed.', ['job' => $jobKey, 'error' => $message]);
        }

        return SystemCronRun::updateOrCreate(
            ['job_key' => $jobKey],
            [
                'status' => $status,
                'message' => mb_substr($message, 0, 2000),
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'last_run_at' => now(),
            ]
        );
    }

    private function backupDatabase(): string
    {
        $path = app(DatabaseBackupService::class)->create();
        return 'Database đã sao lưu: ' . basename($path);
    }

    private function generateSitemap(): string
    {
        $result = app(SitemapService::class)->generateAll();
        return 'Đã tạo sitemap với ' . (int) ($result['total_urls'] ?? 0) . ' URL.';
    }

    private function cleanExpiredSessions(): string
    {
        $cutoff = now()->subMinutes((int) config('session.lifetime', 120));
        $driver = config('session.driver');

        if ($driver === 'database') {
            $table = config('session.table', 'sessions');
            if (!Schema::hasTable($table)) {
                throw new \RuntimeException('Không tìm thấy bảng session ' . $table . '.');
            }
            $deleted = DB::table($table)->where('last_activity', '<', $cutoff->timestamp)->delete();
            return 'Đã xóa ' . $deleted . ' phiên đăng nhập hết hạn.';
        }

        if ($driver === 'file') {
            $directory = config('session.files', storage_path('framework/sessions'));
            if (!is_dir($directory)) {
                return 'Thư mục session chưa được tạo; không có file để dọn.';
            }

            $deleted = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && !$file->isLink() && $file->getMTime() < $cutoff->timestamp && @unlink($file->getPathname())) {
                    $deleted++;
                }
            }
            return 'Đã xóa ' . $deleted . ' file session hết hạn.';
        }

        return 'Session dùng driver ' . ($driver ?: 'không xác định') . ' tự hết hạn; không cần dọn thủ công.';
    }
}
