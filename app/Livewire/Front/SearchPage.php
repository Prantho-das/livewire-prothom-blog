<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('frontend.layouts.app')]
#[Title('অনুসন্ধান')]
class SearchPage extends Component
{
    #[Url(as: 'q', history: true)]
    public string $query = '';

    public ?int $lastId = null;
    public bool $hasMore = false;
    public int $perPage = 15;
    public int $total = 0;

    /** @var array<int, array<string, mixed>> */
    public array $results = [];

    public function mount(): void
    {
        if (strlen($this->query) >= 2) {
            $this->search();
        }
    }

    public function updatedQuery(): void
    {
        $this->reset('results', 'lastId', 'hasMore', 'total');
        if (strlen($this->query) >= 2) {
            $this->search();
        }
    }

    public function search(): void
    {
        if (strlen($this->query) < 2) {
            return;
        }

        $isMySQL = DB::getDriverName() !== 'sqlite';
        $term = trim($this->query);

        $query = Post::query()
            ->published()
            ->join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->where('post_translations.locale', 'bn')
            ->orderByDesc('posts.id')
            ->limit($this->perPage + 1)
            ->select('posts.*');

        if ($this->lastId) {
            $query->where('posts.id', '<', $this->lastId);
        }

        if ($isMySQL) {
            $booleanTerm = '+' . implode('* +', preg_split('/\s+/', $term)) . '*';
            $query->whereRaw(
                'MATCH(post_translations.title, post_translations.content) AGAINST(? IN BOOLEAN MODE)',
                [$booleanTerm]
            )->orderByRaw(
                'MATCH(post_translations.title, post_translations.content) AGAINST(? IN BOOLEAN MODE) DESC',
                [$term]
            );
        } else {
            $query->where(function ($q) use ($term) {
                $q->where('post_translations.title', 'LIKE', "%{$term}%")
                  ->orWhere('post_translations.content', 'LIKE', "%{$term}%");
            });
        }

        $results = $query->with(['translations', 'categories.translations', 'author'])->get();

        $this->hasMore = $results->count() > $this->perPage;
        $chunk = $results->take($this->perPage);

        if ($chunk->isNotEmpty()) {
            $this->lastId = $chunk->last()->id;
            $this->total += $chunk->count();
        }

        foreach ($chunk as $post) {
            $this->results[] = $this->serializePost($post);
        }
    }

    public function loadMore(): void
    {
        $this->search();
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
        $categories = Cache::remember('categories:nav', 3600, function () {
            return Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with('translations')
                ->get();
        });

        $settings = Cache::remember('site:settings', 3600, fn () => Setting::query()->first());

        return view('livewire.front.search-page', compact('categories', 'settings'));
    }
}
