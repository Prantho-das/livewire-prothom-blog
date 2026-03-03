<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OneMillionPostsSeeder extends Seeder
{
    /**
     * Seed 1 Million Posts (and 2M Translations).
     *
     * CAUTION: This will consume substantial disk space and server memory.
     * Estimated time: 15-30 mins depending on server speed.
     */
    public function run(): void
    {
        $faker = Faker::create('bn_BD'); // Bangla Faker
        $enFaker = Faker::create('en_US'); // English Faker

        $totalPosts = 1000000;
        $chunkSize = 1000; // Efficient chunking
        $categories = DB::table('categories')->pluck('id')->toArray();
        $authorId = DB::table('users')->first()?->id ?? 1;

        if (empty($categories)) {
            $this->command->error('No categories found. Run DatabaseSeeder first!');

            return;
        }

        // Clear existing posts for a clean 1M seed
        $this->command->info('Clearing existing posts and translations...');
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::table('post_translations')->truncate();
            DB::table('post_tag')->truncate();
            DB::table('comments')->truncate();
            DB::table('category_post')->truncate();
            DB::table('posts')->truncate();
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('post_translations')->truncate();
            DB::table('post_tag')->truncate();
            DB::table('comments')->truncate();
            DB::table('category_post')->truncate();
            DB::table('posts')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command->info("Seeding $totalPosts posts in chunks of $chunkSize...");

        // Progress Bar (if using Artisan command)
        $bar = $this->command->getOutput()->createProgressBar($totalPosts / $chunkSize);
        $bar->start();

        // Speed optimizations
        DB::disableQueryLog();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA synchronous = OFF');
            DB::statement('PRAGMA journal_mode = MEMORY');
        }

        for ($i = 0; $i < $totalPosts / $chunkSize; $i++) {
            $postsData = [];
            $translationsData = [];
            $categoryPostData = [];
            $now = now()->toDateTimeString();

            for ($j = 0; $j < $chunkSize; $j++) {
                $postId = ($i * $chunkSize) + $j + 1;
                $slug = 'article-'.($postId + 100).'-'.Str::random(5);

                $postsData[] = [
                    'id' => $postId,
                    'slug' => $slug,
                    'author_id' => $authorId,
                    'status' => 'published',
                    'published_at' => $now,
                    'is_featured' => $faker->boolean(5), // 5% featured
                    'is_breaking' => $faker->boolean(2), // 2% breaking
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $categoryPostData[] = [
                    'category_id' => $categories[array_rand($categories)],
                    'post_id' => $postId,
                ];

                // Bangla Translation
                $translationsData[] = [
                    'post_id' => $postId,
                    'locale' => 'bn',
                    'title' => '(বাংলা) '.$faker->realText(50),
                    'excerpt' => $faker->realText(150),
                    'content' => $faker->realText(1000),
                ];

                // English Translation
                $translationsData[] = [
                    'post_id' => $postId,
                    'locale' => 'en',
                    'title' => '(English) '.$enFaker->realText(50),
                    'excerpt' => $enFaker->realText(150),
                    'content' => $enFaker->realText(1000),
                ];
            }

            // Bulk Insert
            DB::transaction(function () use ($postsData, $translationsData, $categoryPostData) {
                DB::table('posts')->insert($postsData);
                DB::table('category_post')->insert($categoryPostData);
                DB::table('post_translations')->insert($translationsData);
            });

            $bar->advance();

            // To prevent memory leak on 1M iterations
            unset($postsData);
            unset($categoryPostData);
            unset($translationsData);
        }

        $bar->finish();
        $this->command->info("\nAll 1 Million posts seeded successfully!");
    }
}
