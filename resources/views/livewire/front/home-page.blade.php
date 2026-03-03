<div>
    {{-- Hero: Featured Posts Grid --}}
    @if(count($featuredPosts) > 0)
    <section class="mb-10" aria-label="প্রধান সংবাদ">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Main feature --}}
            @php
            $fp = $featuredPosts[0];
            @endphp
            <div class="lg:col-span-7">
                <article class="relative rounded-2xl overflow-hidden group card-hover h-[26rem]">
                    <a href="{{ route('post', $fp['slug']) }}" wire:navigate class="block h-full">
                        <div class="h-full overflow-hidden">
                            <x-lazy-img
                                :src="$fp['featured_image'] ? Storage::url($fp['featured_image']) : null"
                                :alt="$fp['title']"
                                cls="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            />
                        </div>
                        <div class="gradient-overlay absolute inset-0"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            @if($fp['category_name'])
                            <span class="category-badge bg-[#c0392b] text-white px-2.5 py-1 rounded mb-2 inline-block">{{ $fp['category_name'] }}</span>
                            @endif
                            @if($fp['is_breaking'])
                            <span class="category-badge bg-yellow-500 text-white px-2.5 py-1 rounded mb-2 inline-block ml-1">🔴 ব্রেকিং</span>
                            @endif
                            <h2 class="text-white text-2xl font-black leading-tight line-clamp-3">{{ $fp['title'] }}</h2>
                            <div class="flex items-center gap-3 mt-2 text-gray-300 text-xs">
                                @if($fp['author'])<span class="font-medium">{{ $fp['author'] }}</span><span>•</span>@endif
                                <span>{{ $fp['published_at'] }}</span>
                                <span>•</span>
                                <span>👁 {{ number_format($fp['views_count']) }}</span>
                            </div>
                        </div>
                    </a>
                </article>
            </div>

            {{-- Side features --}}
            <div class="lg:col-span-5 grid grid-rows-4 gap-3">
                @foreach(array_slice($featuredPosts, 1, 4) as $post)
                <article class="relative rounded-xl overflow-hidden group card-hover">
                    <a href="{{ route('post', $post['slug']) }}" wire:navigate class="block h-full">
                        <div class="h-full min-h-[90px] overflow-hidden">
                            <x-lazy-img
                                :src="$post['featured_image'] ? Storage::url($post['featured_image']) : null"
                                :alt="$post['title']"
                                cls="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                        </div>
                        <div class="gradient-overlay absolute inset-0"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-3">
                            @if($post['category_name'])
                            <span class="category-badge bg-[#c0392b] text-white px-2 py-0.5 rounded mb-1 inline-block text-[10px]">{{ $post['category_name'] }}</span>
                            @endif
                            <h3 class="text-white text-sm font-bold leading-snug line-clamp-2">{{ $post['title'] }}</h3>
                            <p class="text-gray-300 text-xs mt-0.5">{{ $post['published_at'] }}</p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    {{-- Main layout: Content + Sidebar --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-8 space-y-10">

            {{-- Category sections --}}
            @foreach($categories->take(4) as $category)
            @php
                $categoryId = $category->id;
                $catPosts = $categoryPosts[$categoryId] ?? [];
                $catName = $category->translations->where('locale','bn')->first()?->name ?? $category->slug;
            @endphp
            @if(count($catPosts) > 0)
            <section wire:key="cat-{{ $category->id }}" aria-label="{{ $catName }}">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
                        <h2 class="text-xl font-black text-gray-900">{{ $catName }}</h2>
                    </div>
                    <a href="{{ route('category', $category->slug) }}" wire:navigate class="text-sm text-[#c0392b] font-bold hover:underline">আরও দেখুন ›</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($catPosts as $post)
                    <x-post-card :post="$post" wire:key="catpost-{{ $post['id'] }}" />
                    @endforeach
                </div>
            </section>
            @endif
            @endforeach

            {{-- Latest Posts (infinite scroll) --}}
            <section aria-label="সর্বশেষ সংবাদ">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
                    <h2 class="text-xl font-black text-gray-900">সর্বশেষ সংবাদ</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($posts as $post)
                    <x-post-card :post="$post" wire:key="latest-{{ $post['id'] }}" />
                    @empty
                    <div class="col-span-full text-center py-10 text-gray-400">কোনো সংবাদ পাওয়া যায়নি।</div>
                    @endforelse
                </div>

                {{-- Infinite scroll sentinel --}}
                @if($hasMore)
                <div
                    data-infinite-sentinel
                    data-wire-action="loadMorePosts"
                    class="flex justify-center py-8"
                    wire:key="sentinel"
                >
                    <div class="flex items-center gap-2 text-gray-400 text-sm">
                        <svg class="w-5 h-5 animate-spin text-[#c0392b]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        আরও সংবাদ লোড হচ্ছে...
                    </div>
                </div>
                @else
                <p class="text-center text-gray-400 text-sm py-6">সব সংবাদ দেখানো হয়েছে।</p>
                @endif
            </section>

        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-4 space-y-6">

            {{-- E-Paper --}}
            @if(isset($latestEpaper) && $latestEpaper)
            <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-2xl p-6 shadow-lg text-white">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-[#c0392b]/20 rounded-xl flex items-center justify-center text-xl">📰</div>
                    <div>
                        <h3 class="font-black text-base">আজকের ই-পেপার</h3>
                        <p class="text-gray-400 text-xs">{{ $latestEpaper->edition_date?->format('d M Y') }}</p>
                    </div>
                </div>
                <p class="text-gray-300 text-sm mb-4 line-clamp-2">
                    {{ ($latestEpaper->translations->where('locale','bn')->first() ?? $latestEpaper->translations->first())?->title ?? 'আজকের সংস্করণ' }}
                </p>
                <a href="{{ route('epaper') }}" wire:navigate class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-bold py-3 rounded-xl transition-colors">
                    পড়ুন →
                </a>
            </div>
            @endif

            {{-- Popular Posts --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-5">
                    <h3 class="font-black text-gray-900 text-base">সর্বাধিক পঠিত</h3>
                </div>
                <ol class="space-y-0">
                    @foreach(collect($posts)->sortByDesc('views_count')->take(6)->values() as $i => $post)
                    <li wire:key="pop-{{ $post['id'] }}" class="flex gap-3 py-3 border-b border-gray-50 last:border-0 group">
                        <span class="text-3xl font-black text-gray-100 leading-none select-none w-7 flex-shrink-0">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <a href="{{ route('post', $post['slug']) }}" wire:navigate class="text-gray-700 text-sm font-semibold line-clamp-2 leading-snug group-hover:text-[#c0392b] transition-colors">
                            {{ $post['title'] }}
                        </a>
                    </li>
                    @endforeach
                </ol>
            </div>

            {{-- Search Widget --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900 text-base">অনুসন্ধান</h3>
                </div>
                <form action="{{ route('search') }}" method="GET" class="flex gap-2">
                    <input type="text" name="q" placeholder="সংবাদ খুঁজুন..."
                           class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#c0392b]">
                    <button type="submit" class="bg-[#c0392b] text-white px-3 py-2 rounded-lg hover:bg-[#a93226] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
                    </button>
                </form>
            </div>

            {{-- Categories --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900 text-base">বিভাগসমূহ</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                    <a href="{{ route('category', $cat->slug) }}" wire:navigate wire:key="sidebar-cat-{{ $cat->id }}"
                       class="px-3 py-1.5 bg-gray-50 hover:bg-[#c0392b] hover:text-white text-gray-600 text-sm rounded-full border border-gray-100 transition-all font-medium">
                        {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                    </a>
                    @endforeach
                </div>
            </div>

        </aside>
    </div>
</div>
