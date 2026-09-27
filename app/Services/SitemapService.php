<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Page;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class SitemapService
{
    /**
     * Danh sách các loại sitemap được hỗ trợ
     */
    public const TYPES = [
        'movies'   => 'Movie sitemap',
        'genres'   => 'Genre sitemap',
        'episodes' => 'Episode sitemap',
        'actors'   => 'Actor / People sitemap',
        'pages'    => 'Static Pages sitemap',
    ];

    /**
     * Tạo toàn bộ các sitemap con và sitemap index
     */
    public function generateAll(): array
    {
        $results = [];
        $totalUrls = 0;

        foreach (array_keys(self::TYPES) as $type) {
            $count = $this->generateType($type);
            $results[$type] = $count;
            $totalUrls += $count;
        }

        $this->generateIndex();

        return [
            'total_urls' => $totalUrls,
            'details'    => $results,
        ];
    }

    /**
     * Tạo 1 sitemap cụ thể theo loại
     */
    public function generateType(string $type): int
    {
        $xmlContent = '';
        $count = 0;

        switch ($type) {
            case 'movies':
                [$xmlContent, $count] = $this->buildMoviesXml();
                break;
            case 'genres':
                [$xmlContent, $count] = $this->buildGenresXml();
                break;
            case 'episodes':
                [$xmlContent, $count] = $this->buildEpisodesXml();
                break;
            case 'actors':
                [$xmlContent, $count] = $this->buildActorsXml();
                break;
            case 'pages':
                [$xmlContent, $count] = $this->buildPagesXml();
                break;
            default:
                return 0;
        }

        $filePath = public_path("sitemap-{$type}.xml");
        File::put($filePath, $xmlContent);

        // Luôn làm mới lại sitemap index chính
        $this->generateIndex();

        return $count;
    }

    /**
     * Tạo sitemap index chính (sitemap.xml)
     */
    public function generateIndex(): void
    {
        $baseUrl = config('app.url', url('/'));
        $now = Carbon::now()->tz('UTC')->toAtomString();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach (array_keys(self::TYPES) as $type) {
            $filePath = public_path("sitemap-{$type}.xml");
            $lastMod = File::exists($filePath)
                ? Carbon::createFromTimestamp(File::lastModified($filePath))->tz('UTC')->toAtomString()
                : $now;

            $xml .= '    <sitemap>' . PHP_EOL;
            $xml .= '        <loc>' . url("sitemap-{$type}.xml") . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '    </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        File::put(public_path('sitemap.xml'), $xml);
    }

    /**
     * Lấy danh sách thống kê sitemap cho trang quản trị
     */
    public function getSitemapsList(): array
    {
        $list = [];

        foreach (self::TYPES as $type => $label) {
            $fileName = "sitemap-{$type}.xml";
            $filePath = public_path($fileName);
            $exists = File::exists($filePath);

            $count = match ($type) {
                'movies'   => Movie::where('is_published', true)->count() * 2, // Chi tiết + Xem
                'genres'   => Genre::count(),
                'episodes' => Episode::count(),
                'actors'   => Person::count(),
                'pages'    => Page::published()->count() + 3, // Thêm home, movies, auth
                default    => 0,
            };

            $updatedAt = $exists
                ? Carbon::createFromTimestamp(File::lastModified($filePath))->diffForHumans()
                : 'Chưa tạo';

            $size = $exists ? $this->formatFileSize(File::size($filePath)) : '0 KB';

            $list[] = [
                'type'       => $type,
                'name'       => $label,
                'file_name'  => $fileName,
                'url'        => url($fileName),
                'count'      => $count,
                'exists'     => $exists,
                'updated_at' => $updatedAt,
                'size'       => $size,
            ];
        }

        return $list;
    }

    /**
     * Xây dựng nội dung XML cho phim (Movies)
     */
    protected function buildMoviesXml(): array
    {
        $movies = Movie::where('is_published', true)->latest()->get();
        $count = 0;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($movies as $movie) {
            $lastMod = ($movie->updated_at ?: now())->tz('UTC')->toAtomString();

            // Link chi tiết phim
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/movie/' . $movie->slug) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>daily</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.9</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;

            // Link xem phim
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/movie/' . $movie->slug . '/watch') . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.8</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;
        }

        $xml .= '</urlset>';

        return [$xml, $count];
    }

    /**
     * Xây dựng nội dung XML cho thể loại (Genres)
     */
    protected function buildGenresXml(): array
    {
        $genres = Genre::all();
        $count = 0;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($genres as $genre) {
            $lastMod = ($genre->updated_at ?: now())->tz('UTC')->toAtomString();
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/the-loai/' . $genre->slug) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.7</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;
        }

        $xml .= '</urlset>';

        return [$xml, $count];
    }

    /**
     * Xây dựng nội dung XML cho tập phim (Episodes)
     */
    protected function buildEpisodesXml(): array
    {
        $episodes = Episode::with('movie')->whereHas('movie', function ($q) {
            $q->where('is_published', true);
        })->get();

        $count = 0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($episodes as $episode) {
            if (!$episode->movie) continue;
            $lastMod = ($episode->updated_at ?: now())->tz('UTC')->toAtomString();
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/movie/' . $episode->movie->slug . '/watch?episode=' . $episode->id) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.6</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;
        }

        $xml .= '</urlset>';

        return [$xml, $count];
    }

    /**
     * Xây dựng nội dung XML cho diễn viên / đạo diễn (Actors/People)
     */
    protected function buildActorsXml(): array
    {
        $actors = Person::all();
        $count = 0;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($actors as $actor) {
            $lastMod = ($actor->updated_at ?: now())->tz('UTC')->toAtomString();
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/dien-vien/' . $actor->slug) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.5</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;
        }

        $xml .= '</urlset>';

        return [$xml, $count];
    }

    /**
     * Xây dựng nội dung XML cho trang tĩnh (Static Pages)
     */
    protected function buildPagesXml(): array
    {
        $count = 0;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Trang chủ
        $xml .= '    <url>' . PHP_EOL;
        $xml .= '        <loc>' . url('/') . '</loc>' . PHP_EOL;
        $xml .= '        <lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>' . PHP_EOL;
        $xml .= '        <changefreq>hourly</changefreq>' . PHP_EOL;
        $xml .= '        <priority>1.0</priority>' . PHP_EOL;
        $xml .= '    </url>' . PHP_EOL;
        $count++;

        // Danh sách phim
        $xml .= '    <url>' . PHP_EOL;
        $xml .= '        <loc>' . url('/movies') . '</loc>' . PHP_EOL;
        $xml .= '        <lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>' . PHP_EOL;
        $xml .= '        <changefreq>daily</changefreq>' . PHP_EOL;
        $xml .= '        <priority>0.9</priority>' . PHP_EOL;
        $xml .= '    </url>' . PHP_EOL;
        $count++;

        // Các trang tĩnh từ database
        $pages = Page::published()->get();
        foreach ($pages as $page) {
            $lastMod = ($page->updated_at ?: now())->tz('UTC')->toAtomString();
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . url('/' . $page->slug) . '</loc>' . PHP_EOL;
            $xml .= '        <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '        <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '        <priority>0.6</priority>' . PHP_EOL;
            $xml .= '    </url>' . PHP_EOL;
            $count++;
        }

        $xml .= '</urlset>';

        return [$xml, $count];
    }

    /**
     * Format dung lượng file
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 1) . ' KB';
    }
}
