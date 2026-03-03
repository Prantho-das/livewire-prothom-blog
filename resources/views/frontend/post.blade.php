@extends('frontend.layouts.app')

@php
    $locale = 'bn';
    $translation = $post->translations->where('locale', $locale)->first() ?? $post->translations->first();
    $title = $translation?->title ?? 'শিরোনাম নেই';
    $excerpt = $translation?->excerpt ?? '';
    $content = $translation?->content ?? '';
    $metaTitle = $translation?->meta_title ?? $title;
    $metaDesc = $translation?->meta_description ?? $excerpt;
@endphp

@section('title', $metaTitle . ' | ' . ($settings?->site_name ?? 'প্রথম ব্লগ'))

@push('head')
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
@if($post->featured_image)
<meta property="og:image" content="{{ Storage::url($post->featured_image) }}">
@endif
@endpush

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- Article Content --}}
    <article class="lg:col-span-8" itemscope itemtype="https://schema.org/NewsArticle">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-5" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#c0392b] transition-colors">হোম</a>
            @foreach($post->categories->take(1) as $cat)
            <span>/</span>
            <a href="{{ route('category', $cat->slug) }}" class="hover:text-[#c0392b] transition-colors">
                {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
            </a>
            @endforeach
            <span>/</span>
            <span class="text-gray-600 line-clamp-1">{{ $title }}</span>
        </nav>

        {{-- Categories --}}
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($post->categories as $cat)
            <a href="{{ route('category', $cat->slug) }}"
               class="category-badge bg-[#c0392b] text-white px-3 py-1 rounded hover:bg-[#a93226] transition-colors">
                {{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}
            </a>
            @endforeach
            @if($post->is_breaking)
            <span class="category-badge bg-yellow-500 text-white px-3 py-1 rounded">ব্রেকিং নিউজ</span>
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

        {{-- Meta Info --}}
        <div class="flex flex-wrap items-center gap-4 py-4 border-y border-gray-100 mb-6 text-sm text-gray-500">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#c0392b] flex items-center justify-center text-white text-xs font-bold">
                    {{ substr($post->author?->name ?? 'অ', 0, 1) }}
                </div>
                <div>
                    <span class="font-medium text-gray-700 block">{{ $post->author?->name ?? 'অজানা' }}</span>
                    <span class="text-xs" itemprop="datePublished" datetime="{{ $post->published_at?->toISOString() }}">
                        {{ $post->published_at?->format('d M Y, g:i A') }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>{{ number_format($post->views_count) }} বার পঠিত</span>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <figure class="mb-6 rounded-xl overflow-hidden">
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $title }}" class="w-full object-cover max-h-96" itemprop="image">
        </figure>
        @endif

        {{-- Share Buttons --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="text-sm text-gray-500 font-medium">শেয়ার করুন:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"
               class="flex items-center gap-1.5 bg-[#1877f2] text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                ফেসবুক
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($title) }}" target="_blank"
               class="flex items-center gap-1.5 bg-black text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                টুইটার
            </a>
            <button onclick="navigator.clipboard.writeText(window.location.href);this.textContent='✓ কপি!';"
                    class="flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                🔗 কপি লিঙ্ক
            </button>
        </div>

        {{-- Article Body --}}
        <div class="prose-custom text-gray-800" itemprop="articleBody">
            {!! nl2br(e($content)) !!}
        </div>

        {{-- Tags --}}
        @if($post->tags->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-sm font-bold text-gray-700 mb-3">ট্যাগ:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($post->tags as $tag)
                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full border border-gray-200 hover:bg-[#c0392b] hover:text-white hover:border-[#c0392b] transition-all cursor-pointer">
                    # {{ $tag->translations->where('locale','bn')->first()?->name ?? $tag->translations->first()?->name ?? $tag->slug }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Comments Section --}}
        @if($post->comments->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">মন্তব্য ({{ $post->comments->count() }})</h3>
            <div class="space-y-4">
                @foreach($post->comments->take(10) as $comment)
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[#c0392b] flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($comment->name ?? 'অ', 0, 1) }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">{{ $comment->name ?? 'অজানা' }}</span>
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

        {{-- E-Paper Widget --}}
        <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-xl p-5 text-white">
            <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#c0392b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                ই-পেপার
            </h3>
            <a href="{{ route('epaper') }}" class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-semibold py-2.5 rounded-lg transition-colors">
                আজকের পত্রিকা পড়ুন →
            </a>
        </div>

        {{-- Related Posts --}}
        @if($relatedPosts->isNotEmpty())
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="sidebar-widget pl-3 mb-4">
                <h3 class="font-bold text-gray-900">সম্পর্কিত সংবাদ</h3>
            </div>
            @foreach($relatedPosts as $relPost)
                @include('frontend.partials.post-card', ['post' => $relPost, 'size' => 'small'])
            @endforeach
        </div>
        @endif

        {{-- Categories --}}
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

{{-- Related Posts Section (Bottom) --}}
@if($relatedPosts->isNotEmpty())
<section class="mt-12 pt-8 border-t border-gray-200">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
        <h2 class="text-xl font-bold text-gray-900">আরও পড়ুন</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($relatedPosts as $post)
            @include('frontend.partials.post-card', ['post' => $post, 'size' => 'normal'])
        @endforeach
    </div>
</section>
@endif

@endsection
