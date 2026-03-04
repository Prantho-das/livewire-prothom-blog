<div>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#1a1a2e] to-[#c0392b] rounded-2xl p-8 mb-8">
        <h1 class="text-3xl font-black text-white mb-5 flex items-center gap-3">
            <span class="text-3xl">🔍</span> সংবাদ অনুসন্ধান
        </h1>

        {{-- Search Input with live Livewire binding --}}
        <div class="flex gap-2 max-w-2xl">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="query"
                    placeholder="সংবাদ খুঁজুন..."
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-3 rounded-xl text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white shadow-sm"
                    autocomplete="off"
                    autofocus
                >
                <div wire:loading wire:target="query" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="w-4 h-4 animate-spin text-[#c0392b]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        @if(strlen($query) >= 2)
        <p class="text-gray-300 text-sm mt-3">
            @if(count($results) > 0)
                <strong class="text-white">{{ $total }}</strong> টি ফলাফল পাওয়া গেছে
            @else
                <span wire:loading wire:target="query">খুঁজছে...</span>
                <span wire:loading.remove wire:target="query">কোনো ফলাফল নেই</span>
            @endif
        </p>
        @endif
    </div>

    @if(strlen($query) >= 2)
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- Results --}}
        <div class="lg:col-span-8">
            @if(count($results) > 0)
            <div class="space-y-4 mb-4">
                @foreach($results as $post)
                <article wire:key="search-{{ $post['id'] }}" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 card-hover flex gap-4 group">
                    @if($post['featured_image'])
                    <a href="{{ route('post', $post['slug']) }}" wire:navigate class="flex-shrink-0">
                        <img
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                            data-src="{{ Storage::url($post['featured_image']) }}"
                            alt="{{ $post['title'] }}"
                            class="img-lazy w-32 h-24 object-cover rounded-xl"
                            loading="lazy"
                            width="128"
                            height="96"
                        >
                    </a>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if($post['category_name'])
                        <a href="{{ route('category', $post['category_slug']) }}" wire:navigate
                           class="text-[#c0392b] text-xs font-black uppercase tracking-wide">{{ $post['category_name'] }}</a>
                        @endif
                        <h2 class="text-gray-800 font-bold text-base leading-snug mt-0.5 mb-1.5 line-clamp-2 group-hover:text-[#c0392b] transition-colors">
                            <a href="{{ route('post', $post['slug']) }}" wire:navigate>{{ $post['title'] }}</a>
                        </h2>
                        @if($post['excerpt'])
                        <p class="text-gray-500 text-sm line-clamp-2 mb-2">{{ $post['excerpt'] }}</p>
                        @endif
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            @if($post['author'])<span class="font-medium text-gray-500">{{ $post['author'] }}</span><span>•</span>@endif
                            <span>{{ $post['published_at'] }}</span>
                            <span>•</span>
                            <span>👁 {{ number_format($post['views_count']) }}</span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @else
            <div wire:loading.remove wire:target="query" class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
                <div class="text-5xl mb-4">🔍</div>
                <h3 class="text-gray-600 font-bold text-lg mb-1">"{{ $query }}" এর জন্য কোনো ফলাফল নেই</h3>
                <p class="text-gray-400 text-sm">অন্য শব্দ দিয়ে অনুসন্ধান করুন।</p>
            </div>
            @endif

            {{-- Load More (outside outer @if to avoid Livewire compile issue) --}}
            @if(count($results) > 0 && $hasMore)
            <div data-infinite-sentinel data-wire-action="loadMore" class="flex justify-center py-8" wire:key="search-sentinel">
                <div class="flex items-center gap-2 text-gray-400 text-sm">
                    <svg class="w-4 h-4 animate-spin text-[#c0392b]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    আরও ফলাফল লোড হচ্ছে...
                </div>
            </div>
            @elseif(count($results) > 0)
            <p class="text-center text-gray-400 text-xs py-4">সব ফলাফল দেখানো হয়েছে।</p>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-4">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900">বিভাগসমূহ</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                    <a href="{{ route('category', $cat->slug) }}" wire:navigate wire:key="srch-cat-{{ $cat->id }}"
                       class="px-3 py-1.5 bg-gray-50 hover:bg-[#c0392b] hover:text-white text-gray-600 text-sm rounded-full border border-gray-100 transition-all">
                        {{ $cat->translations->where('locale', app()->getLocale())->first()?->name ?? $cat->slug }}
                    </a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
    @else
    {{-- Prompt when no query --}}
    <div class="text-center py-20 text-gray-400">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-lg font-medium">উপরের বক্সে অনুসন্ধান করুন</p>
        <p class="text-sm mt-2">কমপক্ষে ২টি অক্ষর লিখুন</p>
    </div>
    @endif
</div>
