<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class CategorySection extends Component
{
    public int $categoryId;

    /** @var array<int, array<string, mixed>> */
    public array $posts = [];

    public string $catName = '';

    public string $catSlug = '';

    public function mount(int $categoryId): void
    {
        $this->categoryId = $categoryId;

        $locale = app()->getLocale();
        $this->posts = Cache::remember("cat-section:{$this->categoryId}:{$locale}", 300, function () {
            return Post::query()
                ->published()
                ->whereHas('categories', fn ($q) => $q->where('categories.id', $this->categoryId))
                ->with([
                    'translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title', 'excerpt']),
                    'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                        ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                    'author' => fn ($q) => $q->select(['id', 'name']),
                ])
                ->select(['posts.id', 'posts.slug', 'posts.featured_image', 'posts.is_featured', 'posts.is_breaking', 'posts.views_count', 'posts.published_at', 'posts.author_id'])
                ->latest('published_at')
                ->limit(4)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'slug' => $p->slug,
                    'featured_image' => $p->featured_image,
                    'is_featured' => $p->is_featured ?? false,
                    'is_breaking' => $p->is_breaking ?? false,
                    'title' => $p->translation?->title ?? 'Untitled',
                    'excerpt' => $p->translation?->excerpt ?? '',
                    'published_at' => $p->published_at?->diffForHumans(),
                    'author' => $p->author?->name ?? '',
                    'category_name' => $p->categories->first()?->translation?->name,
                    'category_slug' => $p->categories->first()?->slug,
                    'views_count' => $p->views_count,
                ])
                ->all();
        });

        $category = Cache::remember("cat-meta:{$this->categoryId}:{$locale}", 3600, function () {
            return Category::query()
                ->where('id', $this->categoryId)
                ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])])
                ->first();
        });

        $this->catName = $category?->translation?->name ?? 'বিভাগ';
        $this->catSlug = $category?->slug ?? '';
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="animate-pulse space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-7 bg-gray-200 rounded-full"></div>
                <div class="h-6 w-32 bg-gray-200 rounded"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="h-48 bg-gray-100 rounded-2xl"></div>
                <div class="h-48 bg-gray-100 rounded-2xl"></div>
                <div class="h-48 bg-gray-100 rounded-2xl"></div>
                <div class="h-48 bg-gray-100 rounded-2xl"></div>
            </div>
        </div>
        HTML;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.front.category-section');
    }
}
