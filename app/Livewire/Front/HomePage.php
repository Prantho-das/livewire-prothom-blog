<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\EPaper;
use App\Models\Post;
use App\Models\Setting;
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

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $categoryPosts = [];

    public function mount(): void
    {
        $this->featuredPosts = $this->getFeaturedPosts();
        $this->categoryPosts = $this->getCategoryPosts();
        $this->loadMorePosts();
    }

    public function loadMorePosts(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $query = Post::query()
            ->published()
            ->with([
                'translations' => fn ($q) => $q->select(['post_id', 'locale', 'title', 'excerpt']),
                'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                    ->with(['translations' => fn ($q) => $q->select(['category_id', 'locale', 'name'])]),
                'author' => fn ($q) => $q->select(['id', 'name']),
            ])
            ->select(['id', 'slug', 'featured_image', 'is_featured', 'is_breaking', 'views_count', 'published_at', 'author_id'])
            ->orderByDesc('id')
            ->limit($this->perPage + 1);

        if ($this->lastId) {
            $query->where('id', '<', $this->lastId);
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
        return Cache::remember('posts:featured', 300, function () {
            return Post::query()
                ->published()
                ->where('is_featured', true)
                ->with([
                    'translations' => fn ($q) => $q->select(['post_id', 'locale', 'title', 'excerpt']),
                    'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                        ->with(['translations' => fn ($q) => $q->select(['category_id', 'locale', 'name'])]),
                    'author' => fn ($q) => $q->select(['id', 'name']),
                ])
                ->select(['id', 'slug', 'featured_image', 'is_featured', 'is_breaking', 'views_count', 'published_at', 'author_id'])
                ->orderByDesc('published_at')
                ->limit(5)
                ->get()
                ->map(fn (Post $p) => self::serializePostStatic($p))
                ->values()
                ->all();
        });
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function getCategoryPosts(): array
    {
        $categories = $this->getCachedCategories();

        return Cache::remember('posts:by-category', 300, function () use ($categories) {
            $result = [];
            foreach ($categories->take(4) as $category) {
                $result[$category->id] = Post::query()
                    ->published()
                    ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
                    ->with([
                        'translations' => fn ($q) => $q->select(['post_id', 'locale', 'title', 'excerpt']),
                        'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                            ->with(['translations' => fn ($q) => $q->select(['category_id', 'locale', 'name'])]),
                    ])
                    ->select(['id', 'slug', 'featured_image', 'is_featured', 'is_breaking', 'views_count', 'published_at', 'author_id'])
                    ->orderByDesc('published_at')
                    ->limit(4)
                    ->get()
                    ->map(fn (Post $p) => self::serializePostStatic($p))
                    ->values()
                    ->all();
            }

            return $result;
        });
    }

    private function getCachedCategories(): Collection
    {
        return Cache::remember('categories:nav', 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with(['translations' => fn ($q) => $q->select(['category_id', 'locale', 'name'])])
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
        $locale = 'bn';
        $t = $post->translations->where('locale', $locale)->first() ?? $post->translations->first();
        $cat = $post->categories->first();
        $catT = $cat?->translations->where('locale', $locale)->first() ?? $cat?->translations->first();

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
        $settings = Cache::remember('site:settings', 3600, fn () => Setting::query()->first());
        $categories = $this->getCachedCategories();
        $latestEpaper = Cache::remember('epaper:latest', 1800, function () {
            return EPaper::query()
                ->where('is_active', true)
                ->orderByDesc('edition_date')
                ->select(['id', 'edition_date', 'is_active', 'pdf_path'])
                ->first();
        });

        return view('livewire.front.home-page', compact(
            'categories',
            'latestEpaper',
            'settings',
        ));
    }
}
