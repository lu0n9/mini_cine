<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class StorageDashboardService
{
    private const CATEGORIES = ['images', 'videos', 'subtitles', 'other'];

    public function overview(): array
    {
        $apiServers = ApiKey::query()
            ->where('type', 'video_storage')
            ->orderBy('id')
            ->get(['id', 'name', 'endpoint', 'status', 'expires_at', 'key']);

        if ($apiServers->isEmpty()) {
            if (config('services.supabase.url') && config('services.supabase.service_key')) {
                return ['storages' => [$this->inspectStorage(null)]];
            }

            return ['storages' => []];
        }

        return [
            'storages' => $apiServers
                ->map(fn (ApiKey $apiKey) => $this->inspectStorage($apiKey))
                ->all(),
        ];
    }

    private function inspectStorage(?ApiKey $apiKey): array
    {
        $storage = [
            'id' => $apiKey?->id,
            'name' => $apiKey?->name ?? 'Supabase Storage (.env)',
            'endpoint' => $apiKey?->endpoint ?: config('services.supabase.url'),
            'status' => 'unavailable',
            'bucket' => config('services.supabase.video_bucket', 'movies'),
            'files' => 0,
            'bytes' => 0,
            'formatted_size' => $this->formatGigabytes(0),
            'remaining_quota' => null,
            'categories' => $this->emptyCategories(),
            'complete' => true,
            'message' => 'Không thể đọc metadata từ storage cloud.',
        ];

        if ($apiKey) {
            if ($apiKey->status !== 'active') {
                $storage['status'] = 'inactive';
                $storage['message'] = 'API này đang tắt.';

                return $storage;
            }
            if ($apiKey->expires_at?->isPast()) {
                $storage['status'] = 'expired';
                $storage['message'] = 'API này đã hết hạn.';

                return $storage;
            }
            if (!$apiKey->endpoint || !$apiKey->key) {
                $storage['status'] = 'incomplete';
                $storage['message'] = 'API thiếu endpoint hoặc API key.';

                return $storage;
            }
        }

        try {
            $client = new SupabaseStorageService($apiKey?->id);
            $storage['bucket'] = $client->bucketName();
            $cacheKey = 'admin.storage.supabase.' . $client->inventoryCacheKey();
            $inventory = Cache::remember($cacheKey, now()->addMinutes(10), fn () => $client->inventory());

            $storage['status'] = 'connected';
            $storage['server'] = $client->serverName();
            $storage['files'] = $inventory['files'];
            $storage['bytes'] = $inventory['bytes'];
            $storage['formatted_size'] = $this->formatGigabytes($inventory['bytes']);
            $storage['categories'] = $inventory['categories'];
            foreach ($storage['categories'] as &$category) {
                $category['formatted_size'] = $this->formatGigabytes($category['bytes']);
                $category['percent'] = $inventory['bytes'] > 0
                    ? round($category['bytes'] * 100 / $inventory['bytes'], 1)
                    : 0;
            }
            unset($category);
            $storage['complete'] = $inventory['complete'];
            $storage['message'] = $inventory['complete']
                ? 'Đã lấy dung lượng từ metadata của bucket.'
                : 'Kết quả bị giới hạn số object để tránh quét quá lâu.';
        } catch (Throwable $exception) {
            Log::warning('Could not read storage inventory for Video Storage & CDN API.', [
                'api_id' => $apiKey?->id,
                'error' => $exception->getMessage(),
            ]);
        }

        $storage['endpoint_host'] = parse_url((string) $storage['endpoint'], PHP_URL_HOST) ?: '—';

        return $storage;
    }

    private function emptyCategories(): array
    {
        return array_fill_keys(self::CATEGORIES, ['files' => 0, 'bytes' => 0]);
    }

    private function formatGigabytes(int $bytes): string
    {
        return number_format($bytes / (1024 ** 3), 2, ',', '.') . ' GB';
    }
}
