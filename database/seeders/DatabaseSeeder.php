<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Tìm user test@example.com, nếu chưa có mới tạo mới
    \App\Models\User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]
    );

    $this->call([
            AdminRoleSeeder::class,
        ]);
}
}
