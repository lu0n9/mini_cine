<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function create(?string $directory = null): string
    {
        $directory ??= storage_path('app/private/backups');
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new \RuntimeException('Không thể tạo thư mục lưu backup.');
        }

        $connection = config('database.connections.' . config('database.default'));
        $driver = $connection['driver'] ?? '';
        $name = 'backup-database-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3));
        $path = $directory . '/' . $name . ($driver === 'sqlite' ? '.sqlite' : '.sql');

        if ($driver === 'sqlite') {
            $source = $connection['database'] ?? null;
            if (!$source || !is_file($source) || !copy($source, $path)) {
                throw new \RuntimeException('Không thể sao chép file database SQLite.');
            }
            return $path;
        }

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('Hiện chỉ hỗ trợ backup database MySQL, MariaDB và SQLite.');
        }

        $binary = collect([
            '/Applications/XAMPP/xamppfiles/bin/mysqldump',
            '/usr/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ])->first(fn ($candidate) => is_executable($candidate));
        if (!$binary) {
            throw new \RuntimeException('Không tìm thấy mysqldump.');
        }

        // --routines queries mysql.proc, which can be incompatible after a MariaDB upgrade.
        $arguments = [$binary, '--single-transaction', '--triggers', '--skip-lock-tables'];
        if (!empty($connection['unix_socket'])) {
            $arguments[] = '--socket=' . $connection['unix_socket'];
        } else {
            $arguments[] = '--host=' . ($connection['host'] ?? '127.0.0.1');
            $arguments[] = '--port=' . ($connection['port'] ?? '3306');
        }
        $arguments[] = '--user=' . ($connection['username'] ?? '');
        $arguments[] = $connection['database'];

        $output = fopen($path, 'wb');
        if (!$output) {
            throw new \RuntimeException('Không thể tạo file backup database.');
        }
        try {
            $process = new Process($arguments, base_path(), ['MYSQL_PWD' => (string) ($connection['password'] ?? '')]);
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if ($type === Process::OUT) {
                    fwrite($output, $buffer);
                }
            });
            if (!$process->isSuccessful() || filesize($path) === 0) {
                throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'mysqldump không tạo được dữ liệu.');
            }
        } catch (\Throwable $exception) {
            @unlink($path);
            Log::error('Database backup failed.', ['error' => $exception->getMessage()]);
            throw $exception;
        } finally {
            fclose($output);
        }

        return $path;
    }
}
