<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\EPaper;
use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('frontend.layouts.app')]
#[Title('হোম')]
class HomePage extends Component
{
    public int $perPage = 6;

    public ?int $lastId = null;

    public bool $hasMore = true;

    /** @var array<int, array<string, mixed>> */
    public array $posts = [];

    /** @var array<int, array<string, mixed>> */
    public array $featuredPosts = [];

    public function mount(): void
    {
        $this->featuredPosts = $this->getFeaturedPosts();
        $this->loadMorePosts();
    }

    public function loadMorePosts(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $locale = app()->getLocale();

        $query = Post::query()
            ->published()
            ->with([
                'translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title', 'excerpt']),
                'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                    ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                'author' => fn ($q) => $q->select(['id', 'name']),
            ])
            ->select(['posts.id', 'posts.slug', 'posts.featured_image', 'posts.is_featured', 'posts.is_breaking', 'posts.views_count', 'posts.published_at', 'posts.author_id'])
            ->orderByDesc('posts.id')
            ->limit($this->perPage + 1);

        if ($this->lastId) {
            $query->where('posts.id', '<', $this->lastId);
        }

        $results = $query->get();

        $this->hasMore = $results->count() > $this->perPage;
        $chunk = $results->take($this->perPage);

        if ($chunk->isNotEmpty()) {
            $this->lastId = $chunk->last()->id;
        }

        foreach ($chunk as $post) {
            $this->posts[] = $this->serializePost($post);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getFeaturedPosts(): array
    {
        $locale = app()->getLocale();

        return Cache::remember("posts:featured:{$locale}", 300, function () {
            return Post::query()
                ->published()
                ->where('is_featured', true)
                ->with([
                    'translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title', 'excerpt']),
                    'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                    'author' => fn ($q) => $q->select(['id', 'name']),
                ])
                ->select(['posts.id', 'posts.slug', 'posts.featured_image', 'posts.is_featured', 'posts.is_breaking', 'posts.views_count', 'posts.published_at', 'posts.author_id'])
                ->latest('published_at')
                ->limit(5)
                ->get()
                ->map(fn (Post $p) => self::serializePostStatic($p))
                ->values()
                ->all();
        });
    }

    private function getCachedCategories(): Collection
    {
        $locale = app()->getLocale();

        return Cache::remember("categories:nav:{$locale}", 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])])
                ->select(['id', 'slug', 'parent_id'])
                ->get();
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function serializePost(Post $post): array
    {
        return self::serializePostStatic($post);
    }

    /**
     * Static version so it can be used safely inside Cache::remember closures
     * without capturing $this (which would serialize the entire component).
     *
     * @return array<string, mixed>
     */
    private static function serializePostStatic(Post $post): array
    {
        $locale = app()->getLocale();
        $t = $post->translation;
        $cat = $post->categories->first();
        $catT = $cat?->translation;

        return [
            'id' => $post->id,
            'slug' => $post->slug,
            'featured_image' => $post->featured_image,
            'is_featured' => $post->is_featured,
            'is_breaking' => $post->is_breaking,
            'views_count' => $post->views_count,
            'published_at' => $post->published_at?->diffForHumans(),
            'title' => $t?->title ?? 'শিরোনাম নেই',
            'excerpt' => $t?->excerpt ?? '',
            'author' => $post->author?->name,
            'category_name' => $catT?->name ?? '',
            'category_slug' => $cat?->slug ?? '',
        ];
    }

    public function render(): \Illuminate\View\View
    {
        $latestEpaper = Cache::remember('epaper:latest', 1800, function () {
            return EPaper::query()
                ->where('is_active', true)
                ->orderByDesc('edition_date')
                ->select(['id', 'edition_date', 'is_active', 'pdf_path'])
                ->first();
        });

        return view('livewire.front.home-page', compact('latestEpaper'));
    }
}
