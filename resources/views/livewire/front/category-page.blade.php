@php
    $catName = ($category->translations->where('locale','bn')->first() ?? $category->translations->first())?->name ?? $category->slug;
    $catDesc = ($category->translations->where('locale','bn')->first() ?? $category->translations->first())?->description ?? '';
@endphp

<div>

    {{-- Category Header --}}
    <div class="bg-gradient-to-r from-[#1a1a2e] to-[#c0392b] rounded-2xl p-8 mb-8 text-white">
        <nav class="flex items-center gap-2 text-sm text-gray-300 mb-3">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-white transition-colors">হোম</a>
            <span>/</span>
            <span class="text-white font-bold">{{ $catName }}</span>
        </nav>
        <h1 class="text-3xl font-black mb-2">{{ $catName }}</h1>
        @if($catDesc)<p class="text-gray-300 text-sm leading-relaxed max-w-2xl">{{ $catDesc }}</p>@endif
    </div>

    {{-- Sub-categories --}}
    @if($category->children->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-6">
        <span class="text-sm text-gray-400 font-medium self-center">উপ-বিভাগ:</span>
        @foreach($category->children as $child)
        <a href="{{ route('category', $child->slug) }}" wire:navigate wire:key="sub-{{ $child->id }}"
           class="px-4 py-1.5 border border-[#c0392b] text-[#c0392b] hover:bg-[#c0392b] hover:text-white text-sm font-bold rounded-full transition-all">
            {{ $child->translations->where('locale','bn')->first()?->name ?? $child->slug }}
        </a>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- Posts --}}
        <div class="lg:col-span-8">
            @if(count($posts) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($posts as $post)
                <x-post-card :post="$post" wire:key="catpost-{{ $post['id'] }}" />
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-2xl p-16 text-center shadow-sm border border-gray-100">
                <div class="text-5xl mb-4">📋</div>
                <h3 class="text-gray-600 font-bold text-lg mb-1">কোনো সংবাদ নেই</h3>
                <p class="text-gray-400 text-sm">এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি।</p>
                <a href="{{ route('home') }}" wire:navigate class="mt-4 inline-block bg-[#c0392b] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#a93226] transition-colors">
                    হোমে ফিরুন
                </a>
            </div>
            @endif

            {{-- Infinite scroll sentinel (outside the @if block to avoid Livewire compile issue) --}}
            @if(count($posts) > 0 && $hasMore)
            <div data-infinite-sentinel data-wire-action="loadMore" class="flex justify-center py-10 mt-4" wire:key="sentinel">
                <div class="flex items-center gap-2 text-gray-400 text-sm">
                    <svg class="w-5 h-5 animate-spin text-[#c0392b]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    আরও সংবাদ লোড হচ্ছে...
                </div>
            </div>
            @elseif(count($posts) > 0)
            <p class="text-center text-gray-400 text-sm py-8">এই বিভাগের সব সংবাদ দেখানো হয়েছে।</p>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="sidebar-widget pl-3 mb-4">
                    <h3 class="font-black text-gray-900">সব বিভাগ</h3>
                </div>
                <ul class="space-y-1">
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('category', $cat->slug) }}" wire:navigate wire:key="nav-cat-{{ $cat->id }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors
                               {{ $cat->slug === $category->slug ? 'bg-[#c0392b] text-white font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#c0392b]' }}">
                            <span>{{ $cat->translations->where('locale','bn')->first()?->name ?? $cat->slug }}</span>
                            <span class="opacity-60">›</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-gradient-to-br from-[#1a1a2e] to-[#16213e] rounded-2xl p-5 text-white">
                <h3 class="font-black mb-3">📰 ই-পেপার</h3>
                <a href="{{ route('epaper') }}" wire:navigate class="block w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-center text-sm font-bold py-3 rounded-xl transition-colors">
                    পড়ুন →
                </a>
            </div>
        </aside>
    </div>
</div>
