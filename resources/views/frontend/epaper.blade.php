@extends('frontend.layouts.app')

@section('title', 'ই-পেপার | ' . ($settings?->site_name ?? 'প্রথম ব্লগ'))

@section('content')

{{-- Header --}}
<div class="bg-gradient-to-r from-[#1a1a2e] to-[#16213e] rounded-2xl p-8 mb-8 text-white">
    <h1 class="text-3xl font-black mb-2 flex items-center gap-3">
        <svg class="w-8 h-8 text-[#c0392b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        ই-পেপার আর্কাইভ
    </h1>
    <p class="text-gray-300">সকল সংস্করণের ডিজিটাল পত্রিকা এখানে পাওয়া যাবে</p>
</div>

@if($epapers->isNotEmpty())
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-8">
    @foreach($epapers as $epaper)
    @php
        $t = $epaper->translations->where('locale','bn')->first() ?? $epaper->translations->first();
        $title = $t?->title ?? 'সংস্করণ';
    @endphp
    <article class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 card-hover group">
        {{-- Cover Image or Placeholder --}}
        <div class="relative h-52 bg-gradient-to-br from-[#1a1a2e] via-[#16213e] to-gray-900 flex items-center justify-center overflow-hidden">
            <div class="text-center text-white p-4">
                <div class="text-4xl mb-2">📰</div>
                <div class="text-sm font-medium opacity-80">{{ $title }}</div>
            </div>
            <div class="absolute top-2 right-2">
                <span class="bg-[#c0392b] text-white text-xs font-bold px-2 py-1 rounded">
                    {{ $epaper->edition_date?->format('d M') }}
                </span>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-4">
            <h2 class="text-gray-800 font-bold text-sm line-clamp-2 mb-1 group-hover:text-[#c0392b] transition-colors">
                {{ $title }}
            </h2>
            <p class="text-gray-400 text-xs mb-3">
                {{ $epaper->edition_date?->format('l, d F Y') }}
            </p>
            @if($epaper->pdf_path)
            <a href="{{ Storage::url($epaper->pdf_path) }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full bg-[#c0392b] hover:bg-[#a93226] text-white text-xs font-semibold py-2 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3M3 17V7a2 2 0 0 1 2-2h6l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                PDF ডাউনলোড
            </a>
            @else
            <button disabled class="w-full bg-gray-100 text-gray-400 text-xs font-semibold py-2 rounded-lg cursor-not-allowed">
                শীঘ্রই আসছে
            </button>
            @endif
        </div>
    </article>
    @endforeach
</div>

{{-- Pagination --}}
<div class="flex justify-center">
    {{ $epapers->links() }}
</div>

@else
<div class="bg-white rounded-xl p-16 text-center shadow-sm border border-gray-100">
    <div class="text-6xl mb-4">📰</div>
    <h3 class="text-gray-600 font-semibold text-lg mb-1">কোনো ই-পেপার পাওয়া যায়নি</h3>
    <p class="text-gray-400 text-sm">এখনো কোনো ই-পেপার আপলোড করা হয়নি।</p>
    <a href="{{ route('home') }}" class="mt-4 inline-block bg-[#c0392b] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#a93226] transition-colors">হোমে ফিরুন</a>
</div>
@endif

@endsection
