<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorageService
{
    protected string $baseUrl;
    protected string $serviceKey;
    protected string $bucket;
    protected string $serverName = 'Supabase';

    public function __construct(?int $serverId = null)
    {
        if ($serverId) {
            $apiKey = ApiKey::where('id', $serverId)
                ->where('type', 'video_storage')
                ->where('status', 'active')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->first();

            if (!$apiKey || !$apiKey->endpoint || !$apiKey->key) {
                throw new RuntimeException('API lưu trữ đã chọn không tồn tại, đang tắt hoặc thiếu cấu hình.');
            }

            $this->useApiKey($apiKey);
        } else {
            $configuredApis = ApiKey::where('type', 'video_storage')->exists();
            $apiKey = ApiKey::where('type', 'video_storage')
                ->where('status', 'active')
                ->whereNotNull('endpoint')
                ->whereNotNull('key')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->orderBy('id')
                ->first();

            if ($apiKey) {
                $this->useApiKey($apiKey);
            } elseif ($configuredApis) {
                throw new RuntimeException('Không có API lưu trữ video nào đang hoạt động. Hãy bật một dịch vụ trong trang Quản lý API.');
            } else {
                $this->loadFromConfig();
            }
        }

        if ($this->baseUrl === '') {
            throw new RuntimeException(
                'SUPABASE_URL chưa được cấu hình. Hãy kiểm tra Quản lý API hoặc file .env.'
            );
        }

        if ($this->serviceKey === '') {
            throw new RuntimeException(
                'SUPABASE_SERVICE_KEY chưa được cấu hình. Hãy kiểm tra Quản lý API hoặc file .env.'
            );
        }

        if ($this->bucket === '') {
            throw new RuntimeException(
                'SUPABASE_VIDEO_BUCKET chưa được cấu hình.'
            );
        }
    }

    protected function useApiKey(ApiKey $apiKey): void
    {
        $this->baseUrl = rtrim($apiKey->endpoint, '/');
        $this->serviceKey = $apiKey->key;
        $this->bucket = (string) config('services.supabase.video_bucket', 'movies');
        $this->serverName = $apiKey->name;
    }

    public function serverName(): string
    {
        return $this->serverName;
    }

    public function bucketName(): string
    {
        return $this->bucket;
    }

    public function inventoryCacheKey(): string
    {
        return sha1($this->baseUrl . '|' . $this->bucket);
    }

    /**
     * Read object metadata from the configured bucket without downloading files.
     * The result is capped because this method is used by an interactive admin page.
     *
     * @return array{files:int,bytes:int,categories:array,complete:bool}
     */
    public function inventory(int $maxFiles = 10000, int $maxDirectories = 400): array
    {
        $pageSize = 1000;
        $queue = [''];
        $visited = [];
        $result = [
            'files' => 0,
            'bytes' => 0,
            'categories' => [
                'images' => ['files' => 0, 'bytes' => 0],
                'videos' => ['files' => 0, 'bytes' => 0],
                'subtitles' => ['files' => 0, 'bytes' => 0],
                'other' => ['files' => 0, 'bytes' => 0],
            ],
            'complete' => true,
        ];

        while ($queue !== []) {
            $batch = [];
            while ($queue !== [] && count($batch) < 20) {
                $prefix = array_shift($queue);
                if (isset($visited[$prefix])) {
                    continue;
                }

                $visited[$prefix] = true;
                if (count($visited) > $maxDirectories) {
                    $result['complete'] = false;
                    break 2;
                }
                $batch[] = $prefix;
            }

            if ($batch === []) {
                continue;
            }

            $responses = Http::pool(function (Pool $pool) use ($batch, $pageSize) {
                foreach ($batch as $index => $prefix) {
                    $pool->as('directory-' . $index)
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $this->serviceKey,
                            'apikey' => $this->serviceKey,
                            'Accept' => 'application/json',
                        ])
                        ->connectTimeout(2)
                        ->timeout(4)
                        ->post($this->baseUrl . '/storage/v1/object/list/' . rawurlencode($this->bucket), [
                            'prefix' => $prefix,
                            'limit' => $pageSize,
                            'offset' => 0,
                            'sortBy' => ['column' => 'name', 'order' => 'asc'],
                        ]);
                }
            }, 20);

            foreach ($batch as $index => $prefix) {
                $response = $responses['directory-' . $index] ?? null;
                if ($response instanceof Throwable) {
                    throw $response;
                }
                if (!$response || !$response->successful()) {
                    throw new RuntimeException('Supabase Storage object listing failed.');
                }

                $offset = 0;
                $entries = $response->json();
                do {
                    if (!is_array($entries)) {
                        throw new RuntimeException('Supabase Storage returned an invalid object list.');
                    }

                    foreach ($entries as $entry) {
                        if (!is_array($entry) || empty($entry['name'])) {
                            continue;
                        }

                        $isFolder = !isset($entry['id']) && empty($entry['metadata']);
                        if ($isFolder) {
                            $queue[] = ($prefix === '' ? '' : rtrim($prefix, '/') . '/') . trim($entry['name'], '/') . '/';
                            continue;
                        }

                        if ($result['files'] >= $maxFiles) {
                            $result['complete'] = false;
                            break 4;
                        }

                        $size = max(0, (int) data_get($entry, 'metadata.size', 0));
                        $category = $this->storageCategory((string) $entry['name']);
                        $result['files']++;
                        $result['bytes'] += $size;
                        $result['categories'][$category]['files']++;
                        $result['categories'][$category]['bytes'] += $size;
                    }

                    $entryCount = count($entries);
                    $offset += $entryCount;
                    if ($entryCount === $pageSize) {
                        $nextPage = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->serviceKey,
                            'apikey' => $this->serviceKey,
                            'Accept' => 'application/json',
                        ])->connectTimeout(2)->timeout(4)->post(
                            $this->baseUrl . '/storage/v1/object/list/' . rawurlencode($this->bucket),
                            [
                                'prefix' => $prefix,
                                'limit' => $pageSize,
                                'offset' => $offset,
                                'sortBy' => ['column' => 'name', 'order' => 'asc'],
                            ]
                        );

                        if (!$nextPage->successful()) {
                            throw new RuntimeException('Supabase Storage object listing failed.');
                        }
                        $entries = $nextPage->json();
                    }
                } while ($entryCount === $pageSize);
            }
        }

        return $result;
    }

    /**
     * Load configuration from .env / config.
     */
    protected function loadFromConfig(): void
    {
        $this->baseUrl = rtrim(
            (string) config('services.supabase.url'),
            '/'
        );

        $this->serviceKey = (string) config(
            'services.supabase.service_key'
        );

        $this->bucket = (string) config(
            'services.supabase.video_bucket',
            'movies'
        );
    }

    /**
     * Upload file lên Supabase Storage.
     */
    public function upload(
        string $path,
        string $contents,
        string $contentType = 'application/octet-stream'
    ): string {
        $path = ltrim($path, '/');

        $url = $this->storageUrl($path);

        $headers = [
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'apikey' => $this->serviceKey,
            'Content-Type' => $contentType,
            'x-upsert' => 'true',
        ];

        $response = Http::withHeaders($headers)
            ->withBody($contents, $contentType)
            ->put($url);

        // A fresh Supabase project may not have the configured bucket yet.
        if ($this->isMissingBucket($response)) {
            $this->createBucket();

            $response = Http::withHeaders($headers)
                ->withBody($contents, $contentType)
                ->put($url);
        }

        $apiKey = ApiKey::where('type', 'video_storage')
            ->where('status', 'active')
            ->where('endpoint', $this->baseUrl)
            ->where('key', $this->serviceKey)
            ->first();
        $apiKey?->recordRequest();

        if (!$response->successful()) {
            throw new RuntimeException(
                'Upload Supabase thất bại [' .
                $response->status() .
                ']: ' .
                $response->body() .
                '. Hãy kiểm tra endpoint, service key và bucket "' . $this->bucket . '" trong cấu hình Supabase.'
            );
        }

        return $path;
    }

    protected function isMissingBucket($response): bool
    {
        return $response->status() === 404
            || $response->json('code') === 'NoSuchBucket';
    }

    /** Create the public bucket required by the app's public video URLs. */
    protected function createBucket(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'apikey' => $this->serviceKey,
            'Accept' => 'application/json',
        ])->post($this->baseUrl . '/storage/v1/bucket', [
            'id' => $this->bucket,
            'name' => $this->bucket,
            'public' => true,
        ]);

        // A concurrent upload may have created it after our upload got 404.
        $errorText = strtolower((string) ($response->json('message') ?? $response->body()));
        if (
            $response->successful()
            || $response->status() === 409
            || str_contains($errorText, 'already exists')
        ) {
            return;
        }

        throw new RuntimeException(
            'Không thể tạo bucket Supabase "' . $this->bucket . '" [' .
            $response->status() . ']: ' . $response->body() .
            '. Hãy kiểm tra quyền tạo bucket của service key.'
        );
    }

    /**
     * Lấy public URL của file.
     */
    public function publicUrl(string $path): string
    {
        $path = ltrim($path, '/');

        return $this->baseUrl .
            '/storage/v1/object/public/' .
            $this->bucket .
            '/' .
            $path;
    }

    /**
     * Tạo Storage API URL.
     */
    protected function storageUrl(string $path): string
    {
        return $this->baseUrl .
            '/storage/v1/object/' .
            $this->bucket .
            '/' .
            $path;
    }

    protected function storageCategory(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match (true) {
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'], true) => 'images',
            in_array($extension, ['srt', 'vtt', 'ass', 'ssa', 'sub'], true) => 'subtitles',
            in_array($extension, ['mp4', 'm4v', 'mov', 'mkv', 'avi', 'webm', 'm3u8', 'ts', 'm4s', 'mpd', 'aac'], true) => 'videos',
            default => 'other',
        };
    }
}
