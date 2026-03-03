<div>

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#1a1a2e] to-[#16213e] rounded-2xl p-8 mb-8 text-white">
        <h1 class="text-3xl font-black mb-2 flex items-center gap-3">
            <span>📰</span> ই-পেপার আর্কাইভ
        </h1>
        <p class="text-gray-300 text-sm">সব সংস্করণের ডিজিটাল পত্রিকা</p>
    </div>

    @if(count($epapers) > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-6">
        @foreach($epapers as $epaper)
        <article wire:key="epaper-{{ $epaper['id'] }}" class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 card-hover group">
            <div class="relative h-52 bg-gradient-to-br from-[#1a1a2e] via-[#16213e] to-[#2c3e50] flex items-center justify-center">
                <div class="text-center text-white px-3">
                    <div class="text-4xl mb-2">🗞️</div>
                    <div class="text-sm font-bold leading-tight line-clamp-2 opacity-90">{{ $epaper['title'] }}</div>
                </div>
                <div class="absolute top-2 right-2">
                    <span class="bg-[#c0392b] text-white text-xs font-black px-2 py-1 rounded">{{ $epaper['edition_short'] }}</span>
                </div>
            </div>
            <div class="p-3.5">
                <h2 class="text-gray-800 font-bold text-xs line-clamp-2 mb-1 leading-snug group-hover:text-[#c0392b] transition-colors">{{ $epaper['title'] }}</h2>
                <p class="text-gray-400 text-xs mb-3">{{ $epaper['edition_date'] }}</p>
                @if($epaper['pdf_path'])
                <a href="{{ Storage::url($epaper['pdf_path']) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-1.5 w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-xs font-bold py-2 rounded-lg transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/></svg>
                    PDF ডাউনলোড
                </a>
                @else
                <button disabled class="w-full bg-gray-100 text-gray-400 text-xs font-bold py-2 rounded-lg cursor-not-allowed">শীঘ্রই আসছে</button>
                @endif
            </div>
        </article>
        @endforeach
    </div>

    {{-- Load more --}}
    @if($hasMore)
    <div data-infinite-sentinel data-wire-action="loadMore" class="flex justify-center py-8" wire:key="epaper-sentinel">
        <div class="flex items-center gap-2 text-gray-400 text-sm">
            <svg class="w-5 h-5 animate-spin text-[#c0392b]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            আরও সংস্করণ লোড হচ্ছে...
        </div>
    </div>
    @else
    <p class="text-center text-gray-400 text-sm py-4">সব সংস্করণ দেখানো হয়েছে।</p>
    @endif

    @else
    <div class="bg-white rounded-2xl p-20 text-center shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">📰</div>
        <h3 class="text-gray-600 font-bold text-lg mb-1">কোনো ই-পেপার নেই</h3>
        <p class="text-gray-400 text-sm">এখনো কোনো ই-পেপার আপলোড করা হয়নি।</p>
        <a href="{{ route('home') }}" wire:navigate class="mt-5 inline-block bg-[#c0392b] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#a93226] transition-colors">হোমে ফিরুন</a>
    </div>
    @endif
</div>
