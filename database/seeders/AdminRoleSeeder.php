<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        AdminRole::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Toàn quyền quản trị hệ thống.',
                'is_super_admin' => true,
                'is_active' => true,
            ]
        );

        AdminRole::updateOrCreate(
            ['slug' => 'movie-manager'],
            [
                'name' => 'Movie Manager',
                'description' => 'Quản lý phim và tập phim.',
                'is_super_admin' => false,
                'is_active' => true,
            ]
        );

        AdminRole::updateOrCreate(
            ['slug' => 'user-manager'],
            [
                'name' => 'User Manager',
                'description' => 'Quản lý người dùng.',
                'is_super_admin' => false,
                'is_active' => true,
            ]
        );

        AdminRole::updateOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderator',
                'description' => 'Quản lý bình luận, đánh giá và báo cáo.',
                'is_super_admin' => false,
                'is_active' => true,
            ]
        );
    }
}