<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\EPaper;
use App\Models\Notice;
use App\Models\Post;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $featuredPosts = Post::query()
            ->published()
            ->where('is_featured', true)
            ->with(['translations', 'categories.translations', 'author'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $breakingPosts = Post::query()
            ->published()
            ->where('is_breaking', true)
            ->with(['translations'])
            ->latest('published_at')
            ->limit(10)
            ->get();

        $latestPosts = Post::query()
            ->published()
            ->with(['translations', 'categories.translations', 'author'])
            ->latest('published_at')
            ->limit(12)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['translations', 'children.translations'])
            ->get();

        $latestEpaper = EPaper::query()
            ->where('is_active', true)
            ->orderByDesc('edition_date')
            ->with('translations')
            ->first();

        $topbarNotice = Notice::query()
            ->where('type', 'topbar')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();

        $headerAd = Advertisement::query()
            ->where('position', 'header')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();

        $settings = Setting::query()->first();

        /** @var array<string, \Illuminate\Database\Eloquent\Collection<int, Post>> $categoryPosts */
        $categoryPosts = [];
        foreach ($categories->take(4) as $category) {
            $categoryPosts[$category->id] = Post::query()
                ->published()
                ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
                ->with(['translations', 'categories.translations'])
                ->latest('published_at')
                ->limit(4)
                ->get();
        }

        return view('frontend.home', compact(
            'featuredPosts',
            'breakingPosts',
            'latestPosts',
            'categories',
            'latestEpaper',
            'topbarNotice',
            'headerAd',
            'settings',
            'categoryPosts'
        ));
    }

    public function category(string $slug): \Illuminate\View\View
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->with(['translations', 'children.translations'])
            ->firstOrFail();

        $posts = Post::query()
            ->published()
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->with(['translations', 'categories.translations', 'author'])
            ->latest('published_at')
            ->paginate(15);

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('translations')
            ->get();

        $settings = Setting::query()->first();

        return view('frontend.category', compact('category', 'posts', 'categories', 'settings'));
    }

    public function post(string $slug): \Illuminate\View\View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->published()
            ->with(['translations', 'categories.translations', 'author', 'tags.translations', 'comments'])
            ->firstOrFail();

        $post->increment('views_count');

        $relatedPosts = Post::query()
            ->published()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $post->categories->pluck('id')))
            ->where('id', '!=', $post->id)
            ->with(['translations', 'categories.translations'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('translations')
            ->get();

        $settings = Setting::query()->first();

        return view('frontend.post', compact('post', 'relatedPosts', 'categories', 'settings'));
    }

    public function epaper(): \Illuminate\View\View
    {
        $epapers = EPaper::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderByDesc('edition_date')
            ->paginate(12);

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('translations')
            ->get();

        $settings = Setting::query()->first();

        return view('frontend.epaper', compact('epapers', 'categories', 'settings'));
    }

    public function search(): \Illuminate\View\View
    {
        $term = request('q', '');

        $posts = Post::query()
            ->published()
            ->when($term, fn ($q) => $q->search($term))
            ->with(['translations', 'categories.translations', 'author'])
            ->latest('published_at')
            ->paginate(15);

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('translations')
            ->get();

        $settings = Setting::query()->first();

        return view('frontend.search', compact('posts', 'categories', 'settings', 'term'));
    }
}
