@extends('frontend.layouts.app')

@section('title', $settings?->site_title ?? 'প্রথম ব্লগ - বাংলাদেশের বিশ্বস্ত সংবাদ')

@section('content')

{{-- Hero Section: Featured Posts --}}
@if($featuredPosts->isNotEmpty())
<section class="mb-10" aria-label="প্রধান সংবাদ">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Featured Post --}}
        @if($featuredPosts->first())
        <div class="lg:col-span-7">
            @include('frontend.partials.post-card', ['post' => $featuredPosts->first(), 'size' => 'large'])
        </div>
        @endif

        {{-- Side Featured Posts --}}
        <div class="lg:col-span-5 grid grid-cols-1 gap-4">
            @foreach($featuredPosts->skip(1)->take(4) as $post)
            <article class="relative rounded-xl overflow-hidden group card-hover h-36 sm:h-40">
                <a href="{{ route('post', $post->slug) }}" class="block h-full">
                    @php($t = $post->translations->where('locale', 'bn')->first() ?? $post->translations->first())
                    @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $t?->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-900"></div>
                    @endif
                    <div class="gradient-overlay absolute inset-0"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        @php($catName = $post->categories->first()?->translations->where('locale','bn')->first()?->name)
                        @if($catName)
                        <span class="category-badge bg-[#c0392b] text-white px-1.5 py-0.5 rounded mb-1 inline-block">{{ $catName }}</span>
                        @endif
                        <h3 class="text-white text-sm font-bold leading-tight line-clamp-2">{{ $t?->title ?? 'শিরোনাম নেই' }}</h3>
                        <p class="text-gray-300 text-xs mt-1">{{ $post->published_at?->diffForHumans() }}</p>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Main Content + Sidebar --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- Main Content --}}
    <div class="lg:col-span-8 space-y-10">

        {{-- Latest News --}}
        <section aria-label="সর্বশেষ সংবাদ">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
                <h2 class="text-xl font-bold text-gray-900">সর্বশেষ সংবাদ</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($latestPosts as $post)
                    @include('frontend.partials.post-card', ['post' => $post, 'size' => 'normal'])
                @empty
                <p class="text-gray-400 col-span-full text-center py-10">কোনো সংবাদ পাওয়া যায়নি।</p>
                @endforelse
            </div>
        </section>

        {{-- Category Sections --}}
        @foreach($categories->take(4) as $category)
        @php($catPosts = $categoryPosts[$category->id] ?? collect())
        @if($catPosts->isNotEmpty())
        <section aria-label="{{ $category->translations->where('locale','bn')->first()?->name ?? 'বিভাগ' }}">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $category->translations->where('locale','bn')->first()?->name ?? $category->slug }}
                    </h2>
                </div>
                <a href="{{ route('category', $category->slug) }}" class="text-sm text-[#c0392b] font-semibold hover:underline flex items-center gap-1">
                    আরও দেখুন <span>›</span>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($catPosts->take(4) as $post)
                @include('frontend.partials.post-card', ['post' => $post, 'size' => 'normal'])
                @endforeach
            </div>
        </section>
        @endif
        @endforeach

    </div>

    {{-- Sidebar --}}
    <aside class="lg:col-span-4 space-y-6">

        {{-- E-Paper Widget --}}
        @if(isset($latestEpaper) && $latestEpaper)
        <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-xl p-5 shadow-md text-white">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-[#c0392b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <h3 class="font-bold text-lg">আজকের ই-পেপার</h3>
            </div>
            <p class="text-gray-300 text-sm mb-1">
                {{ $latestEpaper->translations->where('locale','bn')->first()?->title ?? 'আজকের সংস্করণ' }}
            </p>
            <p class="text-gray-400 text-xs mb-4">
                {{ $latestEpaper->edition_date?->format('d M Y') }}
            </p>
            <a href="{{ route('epaper') }}" class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-semibold py-2.5 rounded-lg transition-colors">
                পড়ুন →
            </a>
        </div>
        @endif

        {{-- Popular Posts Sidebar --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="sidebar-widget pl-3 mb-4">
                <h3 class="font-bold text-gray-900">সর্বাধিক পঠিত</h3>
            </div>
            <div class="space-y-1">
                @forelse($latestPosts->sortByDesc('views_count')->take(6) as $index => $post)
                @php($t = $post->translations->where('locale','bn')->first() ?? $post->translations->first())
                <div class="flex gap-3 py-2.5 border-b border-gray-50 last:border-0 group">
                    <span class="text-2xl font-black text-gray-100 leading-none">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <a href="{{ route('post', $post->slug) }}" class="text-gray-700 text-sm font-medium leading-snug line-clamp-2 group-hover:text-[#c0392b] transition-colors">
                        {{ $t?->title ?? 'শিরোনাম নেই' }}
                    </a>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">কোনো সংবাদ নেই</p>
                @endforelse
            </div>
        </div>

        {{-- Latest Small Posts --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="sidebar-widget pl-3 mb-4">
                <h3 class="font-bold text-gray-900">সর্বশেষ</h3>
            </div>
            @forelse($latestPosts->take(5) as $post)
                @include('frontend.partials.post-card', ['post' => $post, 'size' => 'small'])
            @empty
                <p class="text-gray-400 text-sm text-center py-4">কোনো সংবাদ নেই</p>
            @endforelse
        </div>

        {{-- Categories Widget --}}
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

@endsection
