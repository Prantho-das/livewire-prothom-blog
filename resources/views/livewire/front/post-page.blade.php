@php
    $locale = 'bn';
    $translation = $post->translations->where('locale', $locale)->first() ?? $post->translations->first();
    $title = $translation?->title ?? 'শিরোনাম নেই';
    $excerpt = $translation?->excerpt ?? '';
    $content = $translation?->content ?? '';
@endphp

<div itemscope itemtype="https://schema.org/NewsArticle">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-5 flex-wrap" aria-label="breadcrumb">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-[#c0392b] transition-colors">হোম</a>
        @foreach($post->categories->take(1) as $cat)
        <span class="text-gray-300">/</span>
        <a href="{{ route('category', $cat->slug) }}" wire:navigate class="hover:text-[#c0392b] transition-colors">
            {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
        </a>
        @endforeach
        <span class="text-gray-300">/</span>
        <span class="text-gray-600 line-clamp-1 max-w-xs">{{ $title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- Article --}}
        <article class="lg:col-span-8">

            {{-- Category badges --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->categories as $cat)
                <a href="{{ route('category', $cat->slug) }}" wire:navigate
                   class="category-badge bg-[#c0392b] text-white px-3 py-1 rounded hover:bg-[#a93226] transition-colors">
                    {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                </a>
                @endforeach
                @if($post->is_breaking)
                <span class="category-badge bg-yellow-500 text-white px-3 py-1 rounded">🔴 ব্রেকিং নিউজ</span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight mb-4" itemprop="headline">
                {{ $title }}
            </h1>

            {{-- Excerpt --}}
            @if($excerpt)
            <p class="text-lg text-gray-600 border-l-4 border-[#c0392b] pl-4 mb-5 italic leading-relaxed">
                {{ $excerpt }}
            </p>
            @endif

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 py-4 border-y border-gray-100 mb-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-[#c0392b] flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                        {{ mb_substr($post->author?->name ?? 'অ', 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-700 text-sm">{{ $post->author?->name ?? 'অজানা' }}</p>
                        <time class="text-xs text-gray-400" datetime="{{ $post->published_at?->toISOString() }}" itemprop="datePublished">
                            {{ $post->published_at?->format('d M Y, g:i A') }}
                        </time>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-sm text-gray-400 ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ number_format($post->views_count) }}
                </div>
            </div>

            {{-- Featured Image (eager load since it's above fold) --}}
            @if($post->featured_image)
            <figure class="mb-6 rounded-2xl overflow-hidden">
                <img
                    src="{{ Storage::url($post->featured_image) }}"
                    alt="{{ $title }}"
                    class="w-full object-cover max-h-[28rem]"
                    loading="eager"
                    decoding="async"
                    itemprop="image"
                >
            </figure>
            @endif

            {{-- Share --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <span class="text-sm text-gray-500 font-medium">শেয়ার করুন:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 bg-[#1877f2] text-white text-xs font-bold px-3 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    ফেসবুক
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($title) }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 bg-black text-white text-xs font-bold px-3 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    টুইটার
                </a>
                <button
                    x-data
                    @click="navigator.clipboard.writeText(window.location.href); $el.innerHTML = '✓ কপি হয়েছে!'; setTimeout(() => $el.innerHTML = '🔗 লিঙ্ক কপি', 2000)"
                    class="flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-bold px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    🔗 লিঙ্ক কপি
                </button>
            </div>

            {{-- Article body --}}
            <div class="prose-custom text-gray-800 leading-[1.95] text-[1.05rem]" itemprop="articleBody">
                {!! nl2br(e($content)) !!}
            </div>

            {{-- Tags --}}
            @if($post->tags->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wide">ট্যাগ:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                    <span class="px-3 py-1.5 bg-gray-50 text-gray-600 text-sm rounded-full border border-gray-200 hover:bg-[#c0392b] hover:text-white hover:border-[#c0392b] transition-all cursor-pointer">
                        #{{ $tag->translations->where('locale','bn')->first()?->name ?? $tag->translations->first()?->name ?? '' }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Comments --}}
            @php
            $approvedComments = $post->comments->where('is_approved', true);
            @endphp
            @if($approvedComments->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-black text-gray-900 mb-5">মন্তব্য ({{ $approvedComments->count() }})</h3>
                <div class="space-y-4">
                    @foreach($approvedComments->take(10) as $comment)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#c0392b] to-[#e74c3c] flex items-center justify-center text-white text-xs font-black">
                                {{ mb_substr($comment->name ?? 'অ', 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-gray-700 text-sm">{{ $comment->name ?? 'অজানা' }}</span>
                                <span class="text-gray-400 text-xs ml-2">{{ $comment->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $comment->body }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </article>

        {{-- Sidebar --}}
        <aside class="lg:col-span-4 space-y-6">

            <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-2xl p-5 text-white">
                <h3 class="font-black text-base mb-3 flex items-center gap-2">📰 আজকের ই-পেপার</h3>
                <a href="{{ route('epaper') }}" wire:navigate class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-bold py-3 rounded-xl transition-colors">
                    পড়ুন →
                </a>
            </div>

            @if($relatedPosts->isNotEmpty())
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900">সম্পর্কিত সংবাদ</h3>
                </div>
                @foreach($relatedPosts as $rp)
                @php
                    $rt = $rp->translations->where('locale','bn')->first() ?? $rp->translations->first();
                    $rcat = $rp->categories->first();
                    $rcatName = $rcat?->translations->where('locale','bn')->first()?->name ?? '';
                @endphp
                <div wire:key="related-{{ $rp->id }}" class="flex gap-3 py-3 border-b border-gray-50 last:border-0 group">
                    @if($rp->featured_image)
                    <a href="{{ route('post', $rp->slug) }}" wire:navigate class="flex-shrink-0">
                        <img
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                            data-src="{{ Storage::url($rp->featured_image) }}"
                            alt="{{ $rt?->title }}"
                            class="img-lazy w-20 h-16 object-cover rounded-lg"
                            loading="lazy"
                            width="80"
                            height="64"
                        >
                    </a>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if($rcatName)<span class="text-[#c0392b] text-[10px] font-black uppercase">{{ $rcatName }}</span>@endif
                        <h4 class="text-gray-700 text-sm font-bold leading-snug line-clamp-2 mt-0.5 group-hover:text-[#c0392b] transition-colors">
                            <a href="{{ route('post', $rp->slug) }}" wire:navigate>{{ $rt?->title ?? 'শিরোনাম নেই' }}</a>
                        </h4>
                        <p class="text-gray-400 text-xs mt-1">{{ $rp->published_at?->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900">বিভাগসমূহ</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                    <a href="{{ route('category', $cat->slug) }}" wire:navigate wire:key="pc-cat-{{ $cat->id }}"
                       class="px-3 py-1.5 bg-gray-50 hover:bg-[#c0392b] hover:text-white text-gray-600 text-sm rounded-full border border-gray-100 transition-all">
                        {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                    </a>
                    @endforeach
                </div>
            </div>

        </aside>
    </div>

    {{-- Related Posts Bottom --}}
    @if($relatedPosts->isNotEmpty())
    <section class="mt-12 pt-8 border-t border-gray-200">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
            <h2 class="text-xl font-black text-gray-900">আরও পড়ুন</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($relatedPosts as $rp)
            @php
                $rt = $rp->translations->where('locale','bn')->first() ?? $rp->translations->first();
                $rcat = $rp->categories->first();
                $serialized = [
                    'id' => $rp->id,
                    'slug' => $rp->slug,
                    'featured_image' => $rp->featured_image,
                    'is_featured' => false,
                    'is_breaking' => false,
                    'views_count' => $rp->views_count,
                    'published_at' => $rp->published_at?->diffForHumans(),
                    'title' => $rt?->title ?? 'শিরোনাম নেই',
                    'excerpt' => $rt?->excerpt ?? '',
                    'author' => $rp->author?->name,
                    'category_name' => $rcat?->translations->where('locale','bn')->first()?->name ?? '',
                    'category_slug' => $rcat?->slug ?? '',
                ];
            @endphp
            <x-post-card :post="$serialized" wire:key="more-{{ $rp->id }}" />
            @endforeach
        </div>
    </section>
    @endif

</div>
