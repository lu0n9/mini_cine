<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use App\Services\SitemapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    /**
     * Hiển thị trang cấu hình SEO toàn website
     */
    public function seo()
    {
        $setting = SeoSetting::getSettings();

        return view('admin.pages.seo.seo', compact('setting'));
    }

    /**
     * Cập nhật thông số SEO toàn website
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'site_title'          => 'required|string|max:255',
            'canonical_url'       => 'nullable|url|max:255',
            'site_description'    => 'nullable|string|max:500',
            'keywords'            => 'nullable|string|max:500',
            'google_verification' => 'nullable|string|max:255',
            'bing_verification'   => 'nullable|string|max:255',
            'robots_meta'         => 'required|string',
            'custom_robots_txt'   => 'nullable|string|max:5000',
            'favicon'             => 'nullable|file|mimes:ico,png,svg,jpg,jpeg|max:2048',
            'og_image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'site_title.required' => 'Tiêu đề website không được để trống.',
            'canonical_url.url'   => 'Canonical URL không đúng định dạng URL hợp lệ.',
            'favicon.mimes'       => 'Favicon phải có định dạng .ico, .png, .svg hoặc .jpg',
            'og_image.image'      => 'Ảnh chia sẻ mạng xã hội phải là file ảnh hợp lệ.',
        ]);

        $setting = SeoSetting::getSettings();

        $data = [
            'site_title'          => $request->site_title,
            'canonical_url'       => $request->canonical_url ?: config('app.url', url('/')),
            'site_description'    => $request->site_description,
            'keywords'            => $request->keywords,
            'google_verification' => $request->google_verification,
            'bing_verification'   => $request->bing_verification,
            'robots_meta'         => $request->robots_meta ?: 'index, follow',
            'custom_robots_txt'   => $request->custom_robots_txt,
        ];

        // Xử lý upload Favicon
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $dir = public_path('uploads/seo');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $data['favicon'] = 'uploads/seo/' . $filename;
        }

        // Xử lý upload OG Image
        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $dir = public_path('uploads/seo');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $filename = 'og_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $data['og_image'] = 'uploads/seo/' . $filename;
        }

        $setting->update($data);

        // Đồng bộ robots.txt ra public/robots.txt nếu có nội dung
        if (!empty($request->custom_robots_txt)) {
            File::put(public_path('robots.txt'), $request->custom_robots_txt);
        }

        SeoSetting::clearCache();

        return back()->with('success', 'Đã lưu cài đặt SEO toàn website thành công!');
    }

    /**
     * Hiển thị trang quản lý Sitemap
     */
    public function siteMap(SitemapService $service)
    {
        $sitemaps = $service->getSitemapsList();
        $totalUrls = array_sum(array_column($sitemaps, 'count'));

        $mainSitemapPath = public_path('sitemap.xml');
        $mainExists = File::exists($mainSitemapPath);
        $lastGenerated = $mainExists
            ? Carbon::createFromTimestamp(File::lastModified($mainSitemapPath))->diffForHumans()
            : 'Chưa tạo';

        return view('admin.pages.seo.sitemap', compact(
            'sitemaps',
            'totalUrls',
            'mainExists',
            'lastGenerated'
        ));
    }

    /**
     * Tạo toàn bộ sitemap
     */
    public function generateAllSitemaps(SitemapService $service)
    {
        try {
            $res = $service->generateAll();
            return back()->with('success', "Đã tạo thành công toàn bộ sitemap với {$res['total_urls']} đường dẫn URLs!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi tạo sitemap: ' . $e->getMessage());
        }
    }

    /**
     * Tạo 1 sitemap cụ thể
     */
    public function generateSingleSitemap(string $type, SitemapService $service)
    {
        if (!array_key_exists($type, SitemapService::TYPES)) {
            return back()->with('error', 'Loại sitemap không hợp lệ.');
        }

        try {
            $count = $service->generateType($type);
            $typeName = SitemapService::TYPES[$type];
            return back()->with('success', "Đã tạo mới sitemap [{$typeName}] thành công với {$count} đường dẫn URLs!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi tạo sitemap: ' . $e->getMessage());
        }
    }

    /**
     * Public route: Trả về file sitemap index (sitemap.xml)
     */
    public function serveSitemapIndex(SitemapService $service)
    {
        $filePath = public_path('sitemap.xml');
        if (!File::exists($filePath)) {
            $service->generateAll();
        }

        return Response::file($filePath, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Public route: Trả về file sitemap con (sitemap-{type}.xml)
     */
    public function serveSitemapType(string $type, SitemapService $service)
    {
        if (!array_key_exists($type, SitemapService::TYPES)) {
            abort(404);
        }

        $filePath = public_path("sitemap-{$type}.xml");
        if (!File::exists($filePath)) {
            $service->generateType($type);
        }

        return Response::file($filePath, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
