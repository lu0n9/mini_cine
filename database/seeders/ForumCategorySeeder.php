<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Thảo luận phim',
                'slug' => 'thao-luan-phim',
                'description' => 'Cùng nhau thảo luận về các bộ phim trên Mini Cine.',
                'icon' => '🎬',
                'sort_order' => 1,
            ],
            [
                'name' => 'Review phim',
                'slug' => 'review-phim',
                'description' => 'Chia sẻ cảm nhận và đánh giá phim.',
                'icon' => '⭐',
                'sort_order' => 2,
            ],
            [
                'name' => 'Anime',
                'slug' => 'anime',
                'description' => 'Thảo luận về anime và hoạt hình.',
                'icon' => '🎌',
                'sort_order' => 3,
            ],
            [
                'name' => 'Tin tức',
                'slug' => 'tin-tuc',
                'description' => 'Tin tức điện ảnh và giải trí.',
                'icon' => '📰',
                'sort_order' => 4,
            ],
            [
                'name' => 'Góc cộng đồng',
                'slug' => 'goc-cong-dong',
                'description' => 'Trò chuyện và giao lưu cùng cộng đồng Mini Cine.',
                'icon' => '💬',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category + [
                    'is_active' => true,
                ]
            );
        }
    }
}