@extends('frontend.layouts.app')

@section('title', ($term ? '"' . $term . '" এর ফলাফল' : 'অনুসন্ধান') . ' | ' . ($settings?->site_name ?? 'প্রথম ব্লগ'))

@section('content')

{{-- Search Header --}}
<div class="bg-gradient-to-r from-[#1a1a2e] to-[#c0392b] rounded-2xl p-8 mb-8">
    <h1 class="text-2xl font-black text-white mb-4">
        @if($term)
            "<span class="text-yellow-300">{{ $term }}</span>" এর অনুসন্ধান ফলাফল
        @else
            সংবাদ অনুসন্ধান করুন
        @endif
    </h1>

    <form action="{{ route('search') }}" method="GET" class="flex gap-2 max-w-2xl">
        <input
            type="text"
            name="q"
            value="{{ $term }}"
            placeholder="সংবাদ খুঁজুন..."
            class="flex-1 px-5 py-3 rounded-xl text-gray-900 text-base focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white"
            id="searchInput"
            aria-label="অনুসন্ধান"
            autofocus
        >
        <button type="submit" class="bg-[#c0392b] hover:bg-[#a93226] text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
            খুঁজুন
        </button>
    </form>

    @if($term)
    <p class="text-gray-300 text-sm mt-3">
        মোট <strong class="text-white">{{ $posts->total() }}</strong> টি ফলাফল পাওয়া গেছে
    </p>
    @endif
</div>

@if($term)
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- Results --}}
    <div class="lg:col-span-8">
        @if($posts->isNotEmpty())
        <div class="space-y-4 mb-8">
            @foreach($posts as $post)
            @php
                $t = $post->translations->where('locale','bn')->first() ?? $post->translations->first();
                $title = $t?->title ?? 'শিরোনাম নেই';
                $excerpt = $t?->excerpt ?? '';
                $catName = $post->categories->first()?->translations->where('locale','bn')->first()?->name ?? '';
                $catSlug = $post->categories->first()?->slug ?? '';
            @endphp
            <article class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 card-hover flex gap-4 group">
                @if($post->featured_image)
                <a href="{{ route('post', $post->slug) }}" class="flex-shrink-0">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $title }}" class="w-32 h-24 object-cover rounded-lg group-hover:opacity-90 transition-opacity">
                </a>
                @endif
                <div class="flex-1 min-w-0">
                    @if($catName)
                    <a href="{{ route('category', $catSlug) }}" class="text-[#c0392b] text-xs font-bold uppercase tracking-wide">{{ $catName }}</a>
                    @endif
                    <h2 class="text-gray-800 font-bold text-base leading-snug mt-0.5 mb-1 group-hover:text-[#c0392b] transition-colors">
                        <a href="{{ route('post', $post->slug) }}">{{ $title }}</a>
                    </h2>
                    @if($excerpt)
                    <p class="text-gray-500 text-sm line-clamp-2 mb-2">{{ $excerpt }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span>{{ $post->author?->name }}</span>
                        <span>•</span>
                        <span>{{ $post->published_at?->diffForHumans() }}</span>
                        <span>•</span>
                        <span>👁 {{ number_format($post->views_count) }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $posts->appends(['q' => $term])->links() }}
        </div>
        @else
        <div class="bg-white rounded-xl p-16 text-center shadow-sm border border-gray-100">
            <div class="text-5xl mb-4">🔍</div>
            <h3 class="text-gray-600 font-semibold text-lg mb-1">"{{ $term }}" এর জন্য কোনো ফলাফল পাওয়া যায়নি</h3>
            <p class="text-gray-400 text-sm">অন্য কোনো শব্দ দিয়ে অনুসন্ধান করুন।</p>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <aside class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="sidebar-widget pl-3 mb-4">
                <h3 class="font-bold text-gray-900">বিভাগসমূহ</h3>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                <a href="{{ route('category', $cat->slug) }}"
                   class="px-3 py-1.5 bg-gray-50 hover:bg-[#c0392b] hover:text-white text-gray-600 text-sm rounded-full border border-gray-200 transition-all">
                    {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
                </a>
                @endforeach
            </div>
        </div>
    </aside>

</div>
@endif

@endsection
