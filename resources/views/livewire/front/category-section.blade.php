<section aria-label="{{ $catName }}">
    @if(count($posts) > 0)
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-1 h-7 bg-[#c0392b] rounded-full"></div>
            <h2 class="text-xl font-black text-gray-900">{{ $catName }}</h2>
        </div>
        <a href="{{ route('category', $catSlug) }}" wire:navigate class="text-sm text-[#c0392b] font-bold hover:underline">আরও দেখুন ›</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach($posts as $post)
        <x-post-card :post="$post" wire:key="catpost-{{ $post['id'] }}" />
        @endforeach
    </div>
    @endif
</section>
