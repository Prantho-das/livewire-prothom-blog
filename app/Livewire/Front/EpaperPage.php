<?php

namespace App\Livewire\Front;

use App\Models\Category;
use App\Models\EPaper;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('frontend.layouts.app')]
#[Title('ই-পেপার')]
class EpaperPage extends Component
{
    public ?int $lastId = null;
    public bool $hasMore = true;
    public int $perPage = 12;

    /** @var array<int, array<string, mixed>> */
    public array $epapers = [];

    public function mount(): void
    {
        $this->loadMore();
    }

    public function loadMore(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $query = EPaper::query()
            ->where('is_active', true)
            ->with('translations')
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

        foreach ($chunk as $epaper) {
            $t = $epaper->translations->where('locale', 'bn')->first() ?? $epaper->translations->first();
            $this->epapers[] = [
                'id' => $epaper->id,
                'pdf_path' => $epaper->pdf_path,
                'edition_date' => $epaper->edition_date?->format('l, d F Y'),
                'edition_short' => $epaper->edition_date?->format('d M'),
                'title' => $t?->title ?? 'সংস্করণ',
            ];
        }
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

        return view('livewire.front.epaper-page', compact('categories', 'settings'));
    }
}
