<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Notice;
use App\Models\Setting;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('frontend.layouts.app', function (\Illuminate\View\View $view) {
            $settings = Cache::remember('site:settings', 3600, fn () => Setting::query()->first());

            $categories = Cache::remember('categories:nav', 3600, function () {
                return Category::query()
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->with('translations')
                    ->get();
            });

            $breakingPosts = Cache::remember('posts:breaking', 120, function () {
                return Post::query()
                    ->published()
                    ->where('is_breaking', true)
                    ->with(['translations'])
                    ->orderByDesc('published_at')
                    ->limit(10)
                    ->get()
                    ->map(fn (Post $p) => [
                        'slug' => $p->slug,
                        'title' => ($p->translations->where('locale', 'bn')->first() ?? $p->translations->first())?->title ?? '',
                    ])
                    ->all();
            });

            $topbarNotice = Cache::remember('notice:topbar', 600, function () {
                return Notice::query()
                    ->where('type', 'topbar')
                    ->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
                    ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
                    ->first();
            });

            $headerAd = Cache::remember('ad:header', 600, function () {
                return Advertisement::query()
                    ->where('position', 'header')
                    ->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
                    ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
                    ->first();
            });

            $view->with(compact('settings', 'categories', 'breakingPosts', 'topbarNotice', 'headerAd'));
        });
    }
}
