<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('frontend.layouts.app')]
class PostPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        Post::query()
            ->where('slug', $slug)
            ->published()
            ->increment('views_count');
    }

    public function render(): \Illuminate\View\View
    {
        $post = Cache::remember("post:detail:{$this->slug}", 300, function () {
            return Post::query()
                ->where('slug', $this->slug)
                ->published()
                ->with(['translations', 'categories.translations', 'author', 'tags.translations', 'comments'])
                ->firstOrFail();
        });

        $pageTitle = ($post->translations->where('locale', 'bn')->first() ?? $post->translations->first())?->title ?? 'সংবাদ';

        $relatedPosts = Cache::remember("post:related:{$post->id}", 300, function () use ($post) {
            return Post::query()
                ->published()
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $post->categories->pluck('id')))
                ->where('id', '!=', $post->id)
                ->with(['translations', 'categories.translations'])
                ->orderByDesc('published_at')
                ->limit(4)
                ->get();
        });

        $categories = Cache::remember('categories:nav', 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with('translations')
                ->get();
        });

        $settings = Cache::remember('site:settings', 3600, fn () => Setting::query()->first());

        return view('livewire.front.post-page', compact('post', 'relatedPosts', 'categories', 'settings', 'pageTitle'));
    }
}
