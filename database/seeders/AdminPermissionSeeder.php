<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view' => 'Xem bảng điều khiển',
            'profile.update' => 'Cập nhật hồ sơ quản trị',
            'movies.view' => 'Xem phim',
            'movies.create' => 'Tạo phim',
            'movies.edit' => 'Sửa phim',
            'movies.delete' => 'Xóa phim',
            'episodes.view' => 'Xem tập phim',
            'episodes.create' => 'Tạo tập phim',
            'episodes.edit' => 'Sửa tập phim',
            'episodes.delete' => 'Xóa tập phim',
            'seasons.view' => 'Xem mùa phim',
            'seasons.create' => 'Tạo mùa phim',
            'seasons.edit' => 'Sửa mùa phim',
            'seasons.delete' => 'Xóa mùa phim',
            'servers.view' => 'Xem máy chủ video',
            'servers.create' => 'Tạo máy chủ video',
            'servers.edit' => 'Sửa máy chủ video',
            'servers.delete' => 'Xóa máy chủ video',
            'subtitles.view' => 'Xem phụ đề',
            'subtitles.create' => 'Tạo phụ đề',
            'subtitles.edit' => 'Sửa phụ đề',
            'subtitles.delete' => 'Xóa phụ đề',
            'genres.view' => 'Xem thể loại',
            'genres.create' => 'Tạo thể loại',
            'genres.edit' => 'Sửa thể loại',
            'genres.delete' => 'Xóa thể loại',
            'countries.view' => 'Xem quốc gia',
            'countries.create' => 'Tạo quốc gia',
            'countries.edit' => 'Sửa quốc gia',
            'countries.delete' => 'Xóa quốc gia',
            'people.view' => 'Xem diễn viên và đạo diễn',
            'people.create' => 'Tạo diễn viên hoặc đạo diễn',
            'people.edit' => 'Sửa diễn viên hoặc đạo diễn',
            'people.delete' => 'Xóa diễn viên hoặc đạo diễn',
            'users.view' => 'Xem người dùng',
            'users.ban' => 'Khóa người dùng',
            'users.unban' => 'Mở khóa người dùng',
            'roles.view' => 'Xem vai trò và quyền',
            'roles.create' => 'Tạo vai trò hoặc quyền',
            'roles.edit' => 'Sửa vai trò hoặc quyền',
            'roles.delete' => 'Xóa vai trò hoặc quyền',
            'staff.view' => 'Xem tài khoản quản trị',
            'staff.create' => 'Tạo tài khoản quản trị',
            'staff.edit' => 'Sửa tài khoản quản trị',
            'staff.delete' => 'Xóa tài khoản quản trị',
            'comments.view' => 'Xem bình luận phim',
            'comments.moderate' => 'Kiểm duyệt bình luận phim',
            'comments.delete' => 'Xóa bình luận phim',
            'ratings.view' => 'Xem đánh giá phim',
            'ratings.delete' => 'Xóa đánh giá phim',
            'reports.view' => 'Xem báo cáo',
            'reports.manage' => 'Xử lý và xóa báo cáo',
            'statistics.view' => 'Xem thống kê',
            'interface.banners' => 'Quản lý banner',
            'interface.homepage' => 'Quản lý giao diện trang chủ',
            'interface.menus' => 'Quản lý menu',
            'interface.pages' => 'Quản lý trang nội dung',
            'news.manage' => 'Quản lý tin tức',
            'notifications.view' => 'Xem thông báo',
            'notifications.create' => 'Gửi thông báo',
            'notifications.delete' => 'Xóa chiến dịch thông báo',
            'email.view' => 'Xem cấu hình email',
            'email.update' => 'Cập nhật và kiểm tra email',
            'seo.manage' => 'Quản lý SEO',
            'sitemap.manage' => 'Quản lý sitemap',
            'premium.coupons' => 'Quản lý mã giảm giá Premium',
            'premium.promotions' => 'Quản lý khuyến mại Premium',
            'premium.subscriptions' => 'Xem gói đăng ký Premium',
            'premium.transactions' => 'Xem giao dịch Premium',
            'premium.plans' => 'Quản lý gói Premium',
            'system.activity_log' => 'Xem nhật ký hoạt động',
            'system.api' => 'Quản lý API và cấu hình thanh toán',
            'system.cache' => 'Quản lý bộ nhớ đệm',
            'system.backup' => 'Quản lý sao lưu',
            'system.cron' => 'Quản lý tác vụ định kỳ',
            'system.storage' => 'Xem dung lượng lưu trữ',
            'settings.manage' => 'Quản lý cài đặt hệ thống',
            'videos.view' => 'Xem trạng thái xử lý video',
            'videos.upload' => 'Tải video lên',
            'forum.view' => 'Xem tổng quan diễn đàn',
            'forum.categories.view' => 'Xem danh mục diễn đàn',
            'forum.categories.manage' => 'Quản lý danh mục diễn đàn',
            'forum.posts.view' => 'Xem bài viết diễn đàn',
            'forum.posts.manage' => 'Kiểm duyệt bài viết diễn đàn',
            'forum.comments.view' => 'Xem bình luận diễn đàn',
            'forum.comments.manage' => 'Kiểm duyệt bình luận diễn đàn',
        ];

        foreach ($permissions as $slug => $name) {
            AdminPermission::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'module' => explode('.', $slug, 2)[0],
                    'description' => $name . '.',
                ]
            );
        }
    }
}
