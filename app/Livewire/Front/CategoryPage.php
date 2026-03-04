<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Post;
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

        $category = Cache::remember("cat_id:{$this->slug}", 3600, fn () => Category::where('slug', $this->slug)->firstOrFail(['id']));

        $query = Post::query()
            ->published()
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
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
     * @return array<string, mixed>
     */
    private function serializePost(Post $post): array
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
        $locale = app()->getLocale();
        $category = Cache::remember("category:detail:{$this->slug}:{$locale}", 600, function () {
            return Category::query()
                ->where('slug', $this->slug)
                ->with([
                    'translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name', 'description']),
                    'children' => fn ($q) => $q->select(['id', 'slug', 'parent_id'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                ])
                ->firstOrFail();
        });

        $title = $category->translation?->name ?? $category->slug;

        return view('livewire.front.category-page', compact('category'))
            ->title($title);
    }
}
