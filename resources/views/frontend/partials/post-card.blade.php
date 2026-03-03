@php
    $locale = 'bn';
    $translation = $post->translations->where('locale', $locale)->first() ?? $post->translations->first();
    $title = $translation?->title ?? 'শিরোনাম নেই';
    $excerpt = $translation?->excerpt ?? '';
    $catName = $post->categories->first()?->translations->where('locale', $locale)->first()?->name
        ?? $post->categories->first()?->translations->first()?->name
        ?? '';
    $catSlug = $post->categories->first()?->slug ?? '#';
    $size = $size ?? 'normal'; // 'large', 'normal', 'small'
@endphp

@if($size === 'large')
<article class="relative rounded-xl overflow-hidden group card-hover h-96">
    <a href="{{ route('post', $post->slug) }}" class="block h-full">
        @if($post->featured_image)
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-[#1a1a2e] to-[#c0392b] flex items-center justify-center">
                <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
            </div>
        @endif
        <div class="gradient-overlay absolute inset-0"></div>
        <div class="absolute bottom-0 left-0 right-0 p-5">
            @if($catName)
            <span class="category-badge bg-[#c0392b] text-white px-2 py-1 rounded mb-2 inline-block">{{ $catName }}</span>
            @endif
            @if($post->is_breaking)
            <span class="category-badge bg-yellow-500 text-white px-2 py-1 rounded mb-2 inline-block ml-1">ব্রেকিং</span>
            @endif
            <h2 class="text-white text-xl font-bold leading-tight line-clamp-2">{{ $title }}</h2>
            <div class="flex items-center gap-3 mt-2 text-gray-300 text-xs">
                <span>{{ $post->author?->name }}</span>
                <span>•</span>
                <span>{{ $post->published_at?->diffForHumans() ?? '' }}</span>
                <span>•</span>
                <span>👁 {{ number_format($post->views_count) }}</span>
            </div>
        </div>
    </a>
</article>

@elseif($size === 'small')
<article class="flex gap-3 py-3 border-b border-gray-100 last:border-0 group">
    @if($post->featured_image)
    <a href="{{ route('post', $post->slug) }}" class="flex-shrink-0">
        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $title }}" class="w-20 h-16 object-cover rounded-lg group-hover:opacity-90 transition-opacity">
    </a>
    @endif
    <div class="flex-1 min-w-0">
        @if($catName)
        <a href="{{ route('category', $catSlug) }}" class="text-[#c0392b] text-xs font-bold uppercase tracking-wide">{{ $catName }}</a>
        @endif
        <h3 class="text-gray-800 text-sm font-semibold mt-0.5 line-clamp-2 leading-snug group-hover:text-[#c0392b] transition-colors">
            <a href="{{ route('post', $post->slug) }}">{{ $title }}</a>
        </h3>
        <p class="text-gray-400 text-xs mt-1">{{ $post->published_at?->diffForHumans() }}</p>
    </div>
</article>

@else
<article class="bg-white rounded-xl overflow-hidden shadow-sm card-hover border border-gray-100 group">
    <a href="{{ route('post', $post->slug) }}" class="block">
        <div class="relative h-48 overflow-hidden">
            @if($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/></svg>
                </div>
            @endif
            @if($post->is_breaking)
            <div class="absolute top-2 left-2">
                <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded">ব্রেকিং</span>
            </div>
            @endif
            @if($post->is_featured)
            <div class="absolute top-2 right-2">
                <span class="bg-[#c0392b] text-white text-xs font-bold px-2 py-0.5 rounded">বিশেষ</span>
            </div>
            @endif
        </div>
    </a>
    <div class="p-4">
        @if($catName)
        <a href="{{ route('category', $catSlug) }}" class="text-[#c0392b] text-xs font-bold uppercase tracking-wide mb-1 inline-block">{{ $catName }}</a>
        @endif
        <h3 class="text-gray-800 font-bold text-base leading-snug line-clamp-2 mb-2 group-hover:text-[#c0392b] transition-colors">
            <a href="{{ route('post', $post->slug) }}">{{ $title }}</a>
        </h3>
        @if($excerpt)
        <p class="text-gray-500 text-sm line-clamp-2 mb-3">{{ $excerpt }}</p>
        @endif
        <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-50">
            <span class="font-medium text-gray-500">{{ $post->author?->name }}</span>
            <div class="flex items-center gap-2">
                <span>{{ $post->published_at?->diffForHumans() }}</span>
                <span>•</span>
                <span>👁 {{ number_format($post->views_count) }}</span>
            </div>
        </div>
    </div>
</article>
@endif
