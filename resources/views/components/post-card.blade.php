{{-- Receives $post as array (serialized from Livewire component) --}}
@props(['post'])

<article class="bg-white rounded-xl overflow-hidden shadow-sm card-hover border border-gray-100 group flex flex-col">
    {{-- Image --}}
    <a href="{{ route('post', $post['slug']) }}" wire:navigate class="block relative overflow-hidden h-48 flex-shrink-0 bg-gray-100">
        @if($post['featured_image'])
        <img
            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 9'%3E%3C/svg%3E"
            data-src="{{ Storage::url($post['featured_image']) }}"
            alt="{{ $post['title'] }}"
            class="img-lazy w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            loading="lazy"
            decoding="async"
            width="400"
            height="225"
        >
        @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z"/>
            </svg>
        </div>
        @endif

        @if($post['is_breaking'] ?? false)
        <div class="absolute top-2 left-2">
            <span class="bg-yellow-500 text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wide">🔴 Breaking</span>
        </div>
        @endif
        @if($post['is_featured'] ?? false)
        <div class="absolute top-2 right-2">
            <span class="bg-[#c0392b] text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wide">বিশেষ</span>
        </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-4 flex flex-col flex-1">
        @if($post['category_name'])
        <a href="{{ route('category', $post['category_slug']) }}" wire:navigate class="text-[#c0392b] text-xs font-black uppercase tracking-wide mb-1 inline-block">
            {{ $post['category_name'] }}
        </a>
        @endif

        <h3 class="text-gray-800 font-bold text-sm leading-snug line-clamp-2 mb-2 group-hover:text-[#c0392b] transition-colors flex-1">
            <a href="{{ route('post', $post['slug']) }}" wire:navigate>{{ $post['title'] }}</a>
        </h3>

        @if($post['excerpt'])
        <p class="text-gray-500 text-xs line-clamp-2 mb-3 leading-relaxed">{{ $post['excerpt'] }}</p>
        @endif

        <div class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-50 mt-auto">
            @if($post['author'])<span class="font-medium text-gray-500 truncate max-w-[100px]">{{ $post['author'] }}</span>@endif
            <div class="flex items-center gap-1.5 ml-auto flex-shrink-0">
                <span>{{ $post['published_at'] }}</span>
                <span>·</span>
                <span>👁 {{ number_format($post['views_count']) }}</span>
            </div>
        </div>
    </div>
</article>
