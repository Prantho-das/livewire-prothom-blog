<?php

namespace App\Livewire\Front;

use App\Models\Post;
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
        $locale = app()->getLocale();

        $query = Post::query()
            ->published()
            ->with([
                'translation' => fn ($q) => $q->select(['id', 'post_id', 'locale', 'title', 'excerpt']),
                'categories' => fn ($q) => $q->select(['categories.id', 'categories.slug'])
                    ->with(['translation' => fn ($q) => $q->select(['id', 'category_id', 'locale', 'name'])]),
                'author' => fn ($q) => $q->select(['id', 'name']),
            ])
            ->select(['posts.id', 'posts.slug', 'posts.featured_image', 'posts.views_count', 'posts.published_at', 'posts.author_id'])
            ->limit($this->perPage + 1);

        if ($this->lastId) {
            $query->where('posts.id', '<', $this->lastId);
        }

        if ($isMySQL) {
            $booleanTerm = '+'.implode('* +', preg_split('/\s+/', $term)).'*';
            $query->join('post_translations', 'posts.id', '=', 'post_translations.post_id')
                ->where('post_translations.locale', $locale)
                ->whereRaw(
                    'MATCH(post_translations.title, post_translations.content) AGAINST(? IN BOOLEAN MODE)',
                    [$booleanTerm]
                )->orderByRaw(
                    'MATCH(post_translations.title, post_translations.content) AGAINST(? IN BOOLEAN MODE) DESC',
                    [$term]
                );
        } else {
            $query->whereHas('translations', function ($q) use ($term, $locale) {
                $q->where('locale', $locale)
                    ->where(function ($sub) use ($term) {
                        $sub->where('title', 'LIKE', "%{$term}%")
                            ->orWhere('content', 'LIKE', "%{$term}%");
                    });
            })->orderByDesc('posts.id');
        }

        $results = $query->get();

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
        $locale = app()->getLocale();
        $t = $post->translation;
        $cat = $post->categories->first();
        $catT = $cat?->translation;

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
        return view('livewire.front.search-page');
    }
}
