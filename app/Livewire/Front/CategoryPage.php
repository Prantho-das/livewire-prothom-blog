<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('frontend.layouts.app')]
class CategoryPage extends Component
{
    public string $slug;
    public int $perPage = 15;
    public ?int $lastId = null;
    public bool $hasMore = true;

    /** @var array<int, array<string, mixed>> */
    public array $posts = [];

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->loadMore();
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $category = Category::query()->where('slug', $this->slug)->firstOrFail();

        $query = Post::query()
            ->published()
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->with(['translations', 'categories.translations', 'author'])
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
     * @return array<string, mixed>
     */
    private function serializePost(Post $post): array
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
        $category = Cache::remember("category:detail:{$this->slug}", 600, function () {
            return Category::query()
                ->where('slug', $this->slug)
                ->with(['translations', 'children.translations'])
                ->firstOrFail();
        });

        $categories = Cache::remember('categories:nav', 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with('translations')
                ->get();
        });

        $settings = Cache::remember('site:settings', 3600, fn () => Setting::query()->first());

        $title = ($category->translations->where('locale', 'bn')->first() ?? $category->translations->first())?->name ?? $category->slug;

        return view('livewire.front.category-page', compact('category', 'categories', 'settings'))
            ->title($title . ' | ' . ($settings?->site_name ?? 'প্রথম ব্লগ'));
    }
}
