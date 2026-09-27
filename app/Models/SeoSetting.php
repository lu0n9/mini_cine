<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';

    protected $fillable = [
        'site_title',
        'canonical_url',
        'site_description',
        'keywords',
        'google_verification',
        'bing_verification',
        'favicon',
        'logo',
        'og_image',
        'robots_meta',
        'custom_robots_txt',
    ];

    /**
     * Static in-memory cache cho vòng đời của 1 request HTTP
     */
    protected static ?self $instance = null;

    /**
     * Lấy bản ghi cấu hình SEO duy nhất của website
     * KHÔNG serialize Eloquent model vào database cache để loại bỏ 100% lỗi __PHP_Incomplete_Class
     */
    public static function getSettings(): self
    {
        if (static::$instance instanceof self) {
            return static::$instance;
        }

        $setting = self::first();

        if (!$setting) {
            $setting = self::create([
                'site_title' => 'MINI CINE — Xem phim trực tuyến chất lượng 4K',
                'canonical_url' => config('app.url', url('/')),
                'site_description' => 'MINI CINE: nền tảng xem phim trực tuyến với phim lẻ, phim bộ, phim chiếu rạp chất lượng 4K HDR, phụ đề Việt và thuyết minh.',
                'keywords' => 'xem phim, phim online, phim hd, phim chiếu rạp, mini cine, phim 4k',
                'robots_meta' => 'index, follow',
                'custom_robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\n\nSitemap: " . url('/sitemap.xml'),
            ]);
        }

        return static::$instance = $setting;
    }

    /**
     * Làm mới cache cấu hình SEO
     */
    public static function clearCache(): void
    {
        static::$instance = null;

        try {
            Cache::forget('site_seo_settings');
            Cache::forget('site_seo_settings_data');
        } catch (\Throwable $e) {
            // Bỏ qua nếu cache store có vấn đề
        }
    }
}

