<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'site_name',
        'contact_email',
        'contact_hotline',
        'timezone',
        'maintenance_mode',
        'player_autoplay',
        'player_auto_next',
        'player_pip',
        'allow_registration',
    ];

    protected $casts = [
        'maintenance_mode'         => 'boolean',
        'player_autoplay'          => 'boolean',
        'player_auto_next'         => 'boolean',
        'player_pip'               => 'boolean',
        'allow_registration'       => 'boolean',
    ];

    /**
     * In-memory cache per request (Không lưu Object Eloquent vào database cache tránh __PHP_Incomplete_Class)
     */
    protected static ?self $instance = null;

    /**
     * Lấy bản ghi cài đặt hệ thống duy nhất
     */
    public static function getSettings(): self
    {
        if (static::$instance instanceof self) {
            return static::$instance;
        }

        $setting = self::first();

        if (!$setting) {
            $setting = self::create([
                'site_name'                => 'MINI CINE',
                'contact_email'            => 'contact@minicine.vn',
                'contact_hotline'          => '1900 0000',
                'timezone'                 => 'Asia/Ho_Chi_Minh',
                'maintenance_mode'         => false,
                'player_autoplay'          => true,
                'player_auto_next'         => true,
                'player_pip'               => true,
                'allow_registration'       => true,
            ]);
        }

        return static::$instance = $setting;
    }

    /**
     * Lấy nhanh giá trị một cài đặt theo key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::getSettings();
        return $settings->{$key} ?? $default;
    }

    /**
     * Xóa cache in-memory
     */
    public static function clearCache(): void
    {
        static::$instance = null;
    }
}
