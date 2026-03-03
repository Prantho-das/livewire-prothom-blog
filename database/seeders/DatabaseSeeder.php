<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@prothomalo.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Create Categories
        $categories = [
            ['bn' => 'বাংলাদেশ', 'en' => 'Bangladesh'],
            ['bn' => 'আন্তর্জাতিক', 'en' => 'International'],
            ['bn' => 'খেলা', 'en' => 'Sports'],
            ['bn' => 'বিনোদন', 'en' => 'Entertainment'],
            ['bn' => 'শিক্ষা', 'en' => 'Education'],
        ];

        foreach ($categories as $cat) {
            $category = Category::create([
                'slug' => Str::slug($cat['en']),
                'is_active' => true,
            ]);

            $category->translations()->createMany([
                ['locale' => 'bn', 'name' => $cat['bn'], 'description' => $cat['bn'] . ' সম্পর্কিত খবর'],
                ['locale' => 'en', 'name' => $cat['en'], 'description' => 'News related to ' . $cat['en']],
            ]);
        }

        // 3. Create some Posts
        $allCategories = Category::all();
        
        for ($i = 1; $i <= 10; $i++) {
            $post = Post::create([
                'slug' => "news-article-$i",
                'category_id' => $allCategories->random()->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'is_featured' => $i === 1,
            ]);

            $post->translations()->createMany([
                [
                    'locale' => 'bn',
                    'title' => "বাংলার খবর শিরোনাম $i",
                    'excerpt' => "এই সংবাদের সংক্ষিপ্ত সারমর্ম $i...",
                    'content' => "এটি একটি বিস্তারিত বাংলার খবর। এখানে ১ মিলিয়ন পোস্টের পরিকল্পনা করা হয়েছে।",
                ],
                [
                    'locale' => 'en',
                    'title' => "English News Title $i",
                    'excerpt' => "Short excerpt for this news $i...",
                    'content' => "This is a detailed English news article. Designed for 1M+ posts.",
                ],
            ]);
        }
    }
}
