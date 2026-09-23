<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseStorageService
{
    protected string $baseUrl;
    protected string $serviceKey;
    protected string $bucket;

    public function __construct()
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

        if ($this->baseUrl === '') {
            throw new RuntimeException(
                'SUPABASE_URL chưa được cấu hình.'
            );
        }

        if ($this->serviceKey === '') {
            throw new RuntimeException(
                'SUPABASE_SERVICE_KEY chưa được cấu hình.'
            );
        }

        if ($this->bucket === '') {
            throw new RuntimeException(
                'SUPABASE_VIDEO_BUCKET chưa được cấu hình.'
            );
        }
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

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'apikey' => $this->serviceKey,
            'Content-Type' => $contentType,
            'x-upsert' => 'true',
        ])
            ->withBody($contents, $contentType)
            ->put($url);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Upload Supabase thất bại [' .
                $response->status() .
                ']: ' .
                $response->body()
            );
        }

        return $path;
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
}