<?php

namespace App\Http\View\Composers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FrontendComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $locale = app()->getLocale();

        // 1. Site Settings
        $settings = Cache::remember('site:settings', 3600, function () {
            return Setting::query()->first();
        });

        // 2. Navigation Categories (Heavy caching)
        $categories = Cache::remember("categories:nav:{$locale}", 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])])
                ->select(['id', 'slug', 'parent_id'])
                ->get();
        });

        // 3. Breaking News (Short caching)
        $breakingPosts = Cache::remember("posts:breaking:{$locale}", 300, function () {
            return Post::query()
                ->published()
                ->where('is_breaking', true)
                ->with(['translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title'])])
                ->select(['id', 'slug', 'is_breaking', 'published_at'])
                ->latest('published_at')
                ->limit(10)
                ->get()
                ->map(fn ($post) => [
                    'slug' => $post->slug,
                    'title' => $post->translation?->title ?? 'Untitled',
                ]);
        });

        $view->with([
            'settings' => $settings,
            'categories' => $categories,
            'breakingPosts' => $breakingPosts,
        ]);
    }
}
