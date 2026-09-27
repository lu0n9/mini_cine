<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $table = 'api_keys';

    protected $fillable = [
        'name',
        'key',
        'key_prefix',
        'key_suffix',
        'rate_limit',
        'requests_today',
        'last_used_at',
        'expires_at',
        'status',
        'created_by',
        'type',
        'endpoint',
    ];

    protected $casts = [
        'rate_limit' => 'integer',
        'requests_today' => 'integer',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Lấy chuỗi key đã được che mờ (masking) an toàn.
     */
    public function getMaskedKeyAttribute(): string
    {
        $key = $this->key;
        if (empty($key)) {
            return '••••••••';
        }

        $length = strlen($key);
        if ($length <= 8) {
            return substr($key, 0, 2) . '••••' . substr($key, -2);
        }

        // Nếu là key dạng sk_live_...
        if (str_starts_with($key, 'sk_live_')) {
            $suffix = $this->key_suffix ?: substr($key, -4);
            return 'sk_live_••••' . $suffix;
        }

        // Đối với các key dài như Supabase / Gemini:
        $prefix = substr($key, 0, 6);
        $suffix = substr($key, -4);
        return $prefix . '••••' . $suffix;
    }

    /**
     * Định dạng số lượt sử dụng hôm nay dạng 1.2K, 42.1K hoặc số nguyên.
     */
    public function getFormattedUsageAttribute(): string
    {
        $count = $this->requests_today ?? 0;

        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        }
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }

        return (string) number_format($count);
    }

    /**
     * Danh sách loại API hỗ trợ trong hệ thống.
     */
    public const API_TYPES = [
        'video_storage' => 'Video Storage & CDN',
        'ai_moderation' => 'AI Moderation',
        'movie_metadata' => 'Movie Metadata',
        'client_api' => 'Client API',
        'webhook' => 'Webhook / Internal',
        'custom' => 'Tùy chỉnh',
    ];

    /**
     * Lấy label hiển thị cho loại API.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::API_TYPES[$this->type] ?? $this->type;
    }

    /** Ghi nhận một request đã gửi tới dịch vụ này, theo ngày hiện tại. */
    public function recordRequest(): void
    {
        $count = $this->last_used_at && $this->last_used_at->isToday()
            ? ($this->requests_today ?? 0) + 1
            : 1;

        $this->forceFill([
            'requests_today' => $count,
            'last_used_at' => now(),
        ])->save();
    }

    /**
     * Tạo mới một API Key an toàn.
     */
    public static function createKey(string $name, int $rateLimit = 1000, ?\DateTimeInterface $expiresAt = null, ?string $customKey = null, ?int $requestsToday = 0, string $status = 'active', string $type = 'custom', ?string $endpoint = null): array
    {
        $plainKey = $customKey ? trim($customKey) : ('sk_live_' . Str::random(32));

        // Prevent duplicate key error
        if (static::where('key', $plainKey)->exists()) {
            $plainKey = 'sk_live_' . Str::random(32);
        }

        $prefix = substr($plainKey, 0, 7);
        $suffix = substr($plainKey, -4);

        $apiKey = static::create([
            'name' => $name,
            'key' => $plainKey,
            'key_prefix' => $prefix,
            'key_suffix' => $suffix,
            'rate_limit' => $rateLimit,
            'requests_today' => $requestsToday ?: 0,
            'last_used_at' => now(),
            'expires_at' => $expiresAt,
            'status' => in_array($status, ['active', 'revoked']) ? $status : 'active',
            'created_by' => auth('admin')->id() ?? 1,
            'type' => $type,
            'endpoint' => $endpoint,
        ]);

        return [
            'model' => $apiKey,
            'plain_key' => $plainKey,
        ];
    }

    /**
     * Đưa các thông tin kết nối đã cấu hình trong .env vào danh sách lần đầu.
     * Sau khi tạo, bản ghi trong cơ sở dữ liệu là cấu hình runtime có hiệu lực.
     */
    public static function syncProjectApis(): void
    {
        $integrations = [
            [
                'name' => 'Google Gemini AI (Kiểm duyệt nội dung)',
                'key' => config('services.gemini.api_key'),
                'endpoint' => 'https://generativelanguage.googleapis.com',
                'type' => 'ai_moderation',
                'rate_limit' => 60,
            ],
            [
                'name' => 'Supabase Storage (HLS Video & CDN)',
                'key' => config('services.supabase.service_key'),
                'endpoint' => config('services.supabase.url'),
                'type' => 'video_storage',
                'rate_limit' => 1000,
            ],
        ];

        foreach ($integrations as $integration) {
            if (empty($integration['key']) || ($integration['type'] === 'video_storage' && empty($integration['endpoint']))) {
                continue;
            }

            $existing = static::where('name', $integration['name'])->first();
            if ($existing) {
                continue;
            }

            $key = $integration['key'];
            static::create([
                ...$integration,
                'key_prefix' => substr($key, 0, 7),
                'key_suffix' => substr($key, -4),
                'status' => 'active',
            ]);
        }
    }
}
