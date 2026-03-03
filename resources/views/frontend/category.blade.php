@extends('frontend.layouts.app')

@php
    $locale = 'bn';
    $catName = $category->translations->where('locale', $locale)->first()?->name ?? $category->slug;
    $catDesc = $category->translations->where('locale', $locale)->first()?->description ?? '';
@endphp

@section('title', $catName . ' | ' . ($settings?->site_name ?? 'প্রথম ব্লগ'))

@section('content')

{{-- Category Header --}}
<div class="bg-gradient-to-r from-[#1a1a2e] to-[#c0392b] rounded-2xl p-8 mb-8 text-white">
    <nav class="flex items-center gap-2 text-sm text-gray-300 mb-3" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-white transition-colors">হোম</a>
        <span>/</span>
        <span class="text-white font-medium">{{ $catName }}</span>
    </nav>
    <h1 class="text-3xl font-black mb-2">{{ $catName }}</h1>
    @if($catDesc)
    <p class="text-gray-300 text-sm leading-relaxed max-w-2xl">{{ $catDesc }}</p>
    @endif
    <div class="mt-3 text-sm text-gray-300">
        মোট সংবাদ: <strong class="text-white">{{ $posts->total() }}</strong>
    </div>
</div>

{{-- Sub-categories --}}
@if($category->children->isNotEmpty())
<div class="flex flex-wrap gap-2 mb-6">
    <span class="text-sm text-gray-500 font-medium self-center">উপ-বিভাগ:</span>
    @foreach($category->children as $child)
    <a href="{{ route('category', $child->slug) }}"
       class="px-4 py-1.5 border border-[#c0392b] text-[#c0392b] hover:bg-[#c0392b] hover:text-white text-sm font-medium rounded-full transition-all">
        {{ $child->translations->where('locale','bn')->first()?->name ?? $child->slug }}
    </a>
    @endforeach
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- Posts Grid --}}
    <div class="lg:col-span-8">
        @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            @foreach($posts as $post)
                @include('frontend.partials.post-card', ['post' => $post, 'size' => 'normal'])
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $posts->links() }}
        </div>
        @else
        <div class="bg-white rounded-xl p-16 text-center shadow-sm border border-gray-100">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 0 1 5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            </div>
            <h3 class="text-gray-600 font-semibold text-lg mb-1">কোনো সংবাদ পাওয়া যায়নি</h3>
            <p class="text-gray-400 text-sm">এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি।</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block bg-[#c0392b] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#a93226] transition-colors">হোমে ফিরুন</a>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <aside class="lg:col-span-4 space-y-6">

        {{-- Category List --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="sidebar-widget pl-3 mb-4">
                <h3 class="font-bold text-gray-900">সব বিভাগ</h3>
            </div>
            <ul class="space-y-1">
                @foreach($categories as $cat)
                <li>
                    <a href="{{ route('category', $cat->slug) }}"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors
                              {{ $cat->slug === $category->slug ? 'bg-[#c0392b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-[#c0392b]' }}">
                        <span>{{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}</span>
                        <span class="text-xs opacity-70">›</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- E-Paper Widget --}}
        <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-xl p-5 text-white">
            <h3 class="font-bold mb-3">📰 আজকের ই-পেপার</h3>
            <a href="{{ route('epaper') }}" class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-semibold py-2.5 rounded-lg transition-colors">
                পড়ুন →
            </a>
        </div>

    </aside>
</div>

@endsection
