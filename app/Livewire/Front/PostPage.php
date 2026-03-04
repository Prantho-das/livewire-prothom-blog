<?php

namespace App\Livewire\Front;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
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
        $locale = app()->getLocale();
        $post = Cache::remember("post:detail:{$this->slug}:{$locale}", 300, function () {
            return Post::query()
                ->where('slug', $this->slug)
                ->published()
                ->with([
                    'translation',
                    'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                    'author' => fn ($q) => $q->select(['id', 'name']),
                    'tags' => fn ($q) => $q->select(['tags.id', 'tags.slug'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'tag_id', 'locale', 'name'])]),
                    'comments.user' => fn ($q) => $q->select(['id', 'name']),
                ])
                ->firstOrFail();
        });

        $pageTitle = $post->translation?->title ?? 'সংবাদ';

        $relatedPosts = Cache::remember("post:related:{$post->id}:{$locale}", 300, function () use ($post) {
            return Post::query()
                ->published()
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $post->categories->pluck('id')))
                ->where('posts.id', '!=', $post->id)
                ->with([
                    'translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title', 'excerpt']),
                    'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                ])
                ->select(['posts.id', 'posts.slug', 'posts.featured_image', 'posts.published_at'])
                ->latest('published_at')
                ->limit(4)
                ->get();
        });

        return view('livewire.front.post-page', compact('post', 'relatedPosts', 'pageTitle'));
    }
}
